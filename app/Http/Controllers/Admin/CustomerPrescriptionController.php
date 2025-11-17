<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPrescription;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CustomerPrescriptionController extends Controller
{
    use CrudNotificationTrait;

    /**
     * View customer prescriptions
     */
    public function index(Customer $customer)
    {
        $status = request('status', 'all');

        $prescriptions = $customer->prescriptions()
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('validity_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.prescriptions', compact(
            'customer',
            'prescriptions',
            'status'
        ));
    }

    /**
     * Store prescription
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'prescription_date' => 'required|date',
            'validity_date' => 'required|date|after_or_equal:prescription_date',
            'details' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['customer_id'] = $customer->id;
        $validated['status'] = 'Active';

        CustomerPrescription::create($validated);

        return redirect()->route('admin.customers.prescriptions', $customer)
            ->with('success', 'Prescription created successfully');
    }

    /**
     * Update prescription
     */
    public function update(Request $request, Customer $customer, CustomerPrescription $prescription)
    {
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'validity_date' => 'required|date',
            'details' => 'nullable|string',
            'status' => 'required|in:Active,Expired,Cancelled',
            'remarks' => 'nullable|string',
        ]);

        $prescription->update($validated);

        return redirect()->route('admin.customers.prescriptions', $customer)
            ->with('success', 'Prescription updated successfully');
    }

    /**
     * Delete prescription
     */
    public function destroy(Customer $customer, CustomerPrescription $prescription)
    {
        $prescriptionName = $prescription->name ?? 'Item';
            $prescription->delete();
            $this->notifyDeleted($prescriptionName);
            return back();
    }
}
