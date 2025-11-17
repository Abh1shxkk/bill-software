<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerSpecialRate;
use App\Models\Item;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CustomerSpecialRateController extends Controller
{
    use CrudNotificationTrait;

    /**
     * View customer special rates
     */
    public function index(Customer $customer)
    {
        $rates = $customer->specialRates()
            ->with('item')
            ->paginate(20);

        $items = Item::where('is_deleted', 0)->get();

        return view('admin.customers.special-rates', compact(
            'customer',
            'rates',
            'items'
        ));
    }

    /**
     * Store special rate
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'special_rate' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'rate_type' => 'required|in:Fixed,Percentage',
            'remarks' => 'nullable|string',
        ]);

        $validated['customer_id'] = $customer->id;

        CustomerSpecialRate::create($validated);

        return redirect()->route('admin.customers.special-rates', $customer)
            ->with('success', 'Special rate created successfully');
    }

    /**
     * Update special rate
     */
    public function update(Request $request, Customer $customer, CustomerSpecialRate $rate)
    {
        $validated = $request->validate([
            'special_rate' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'rate_type' => 'required|in:Fixed,Percentage',
            'remarks' => 'nullable|string',
        ]);

        $rate->update($validated);

        return redirect()->route('admin.customers.special-rates', $customer)
            ->with('success', 'Special rate updated successfully');
    }

    /**
     * Delete special rate
     */
    public function destroy(Customer $customer, CustomerSpecialRate $rate)
    {
        $rateName = $rate->name ?? 'Item';
            $rate->delete();
            $this->notifyDeleted($rateName);
            return back();
    }
}
