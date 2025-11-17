<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
class CustomerCopyDiscountController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Show copy discount form
     */
    public function index(Customer $customer)
    {
        $allCustomers = Customer::where('is_deleted', 0)
            ->orderBy('name')
            ->get();
        return view('admin.customers.copy-discount', compact(
            'customer',
            'allCustomers'
        ));
    }

    /**
     * Copy discount from source customer
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'source_customer_id' => 'required|exists:customers,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);
        $sourceCustomer = Customer::findOrFail($validated['source_customer_id']);
        // Get all discounts from source customer
        $sourceDiscounts = $sourceCustomer->discounts()->get();
        if ($sourceDiscounts->count() === 0) {
            return redirect()->route('admin.customers.copy-discount', $customer)
                ->with('error', 'Source customer has no discounts to copy.');
        }

        // Copy discounts to target customer
        foreach ($sourceDiscounts as $sourceDiscount) {
            CustomerDiscount::create([
                'customer_id' => $customer->id,
                'discount_type' => $sourceDiscount->discount_type,
                'discount_percent' => $sourceDiscount->discount_percent,
                'effective_from' => $sourceDiscount->effective_from,
                'effective_to' => $sourceDiscount->effective_to,
                'remarks' => 'Copied from ' . $sourceCustomer->name,
            ]);
        }

        $this->notifyDeleted($customerCopyDiscountName);
            return redirect()->route('admin.customers.discounts', $customer);
    }

    /**
     * API endpoint to get customer discounts
     */
    public function getCustomerDiscounts($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $discounts = $customer->discounts()->get();
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ],
                'discounts' => $discounts->map(fn($d) => [
                    'id' => $d->id,
                    'discount_type' => $d->discount_type,
                    'discount_percent' => $d->discount_percent,
                    'effective_from' => $d->effective_from,
                    'effective_to' => $d->effective_to,
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }
    }
}
