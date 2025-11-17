<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CustomerDiscountController extends Controller
{
    use CrudNotificationTrait;

    /**
     * View customer discounts
     */
    public function index(Customer $customer)
    {
        $type = request('type', 'all');

        $discounts = $customer->discounts()
            ->when($type !== 'all', function ($query) use ($type) {
                $query->where('discount_type', $type);
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.discounts', compact(
            'customer',
            'discounts',
            'type'
        ));
    }

    /**
     * Store discount
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'discount_type' => 'required|in:Breakage,Expiry',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'remarks' => 'nullable|string',
        ]);

        $validated['customer_id'] = $customer->id;

        CustomerDiscount::create($validated);

        return redirect()->route('admin.customers.discounts', $customer)
            ->with('success', 'Discount created successfully');
    }

    /**
     * Update discount
     */
    public function update(Request $request, Customer $customer, CustomerDiscount $discount)
    {
        $validated = $request->validate([
            'discount_percent' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'remarks' => 'nullable|string',
        ]);

        $discount->update($validated);

        return redirect()->route('admin.customers.discounts', $customer)
            ->with('success', 'Discount updated successfully');
    }

    /**
     * Delete discount
     */
    public function destroy(Customer $customer, CustomerDiscount $discount)
    {
        $discountName = $discount->name ?? 'Item';
            $discount->delete();
            $this->notifyDeleted($discountName);
            return back();
    }
}
