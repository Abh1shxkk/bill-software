<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerChallan;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CustomerChallanController extends Controller
{
    use CrudNotificationTrait;

    /**
     * View customer challans
     */
    public function index(Customer $customer)
    {
        $status = request('status', 'all');

        $challans = $customer->challans()
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('delivery_status', $status);
            })
            ->orderBy('challan_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.challans', compact(
            'customer',
            'challans',
            'status'
        ));
    }

    /**
     * Store challan
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'challan_number' => 'required|string|unique:customer_challans',
            'challan_date' => 'required|date',
            'total_items' => 'required|integer|min:1',
            'pending_items' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        $validated['customer_id'] = $customer->id;
        $validated['delivery_status'] = 'Pending';

        CustomerChallan::create($validated);

        return redirect()->route('admin.customers.challans', $customer)
            ->with('success', 'Challan created successfully');
    }

    /**
     * Update challan status
     */
    public function updateStatus(Request $request, Customer $customer, CustomerChallan $challan)
    {
        $validated = $request->validate([
            'delivery_status' => 'required|in:Pending,Partial,Delivered,Cancelled',
            'pending_items' => 'required|integer|min:0',
        ]);

        $challan->update($validated);

        return redirect()->route('admin.customers.challans', $customer)
            ->with('success', 'Challan status updated successfully');
    }

    /**
     * Delete challan
     */
    public function destroy(Customer $customer, CustomerChallan $challan)
    {
        $challanName = $challan->name ?? 'Item';
            $challan->delete();
            $this->notifyDeleted($challanName);
            return back();
    }
}
