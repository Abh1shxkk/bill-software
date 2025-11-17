<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\GeneralLedger;
use App\Models\CashBankBook;
use App\Models\SaleLedger;
use App\Models\PurchaseLedger;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AllLedgerController extends Controller
{
    public function index(Request $request)
    {
        $searchField = $request->get('search_field', 'all');
        $searchTerm = $request->get('search', '');
        $ledgerType = $request->get('ledger_type', '');
        $perPage = 10; // Items per page
        $page = $request->get('page', 1);

        // Debug: Log the received parameters
        \Log::info('AllLedger Request Parameters:', [
            'search_field' => $searchField,
            'search' => $searchTerm,
            'ledger_type' => $ledgerType,
            'page' => $page
        ]);

        // Initialize collections
        $customers = collect();
        $suppliers = collect();
        $generalLedgers = collect();
        $cashBankBooks = collect();
        $saleLedgers = collect();
        $purchaseLedgers = collect();

        // Fetch Customers if no ledger type filter or if Customer is selected
        if (!$ledgerType || $ledgerType === 'Customer') {
            $customers = Customer::select('id', 'name', 'code', 'opening_balance', 'balance_type', 'address', 'address_line2', 'address_line3', 'mobile', 'email', 'telephone_office', 'telephone_residence', 'fax_number', 'contact_person1', 'contact_person2', 'mobile_contact1', 'mobile_contact2', 'city', 'state_name', 'pin_code', 'gst_number', 'pan_number', 'aadhar_number', 'credit_limit')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('code', 'like', "%{$searchTerm}%");
                    case 'mobile':
                        return $query->where('mobile', 'like', "%{$searchTerm}%");
                    case 'telephone':
                        return $query->where(function($q) use ($searchTerm) {
                            $q->where('telephone_office', 'like', "%{$searchTerm}%")
                              ->orWhere('telephone_residence', 'like', "%{$searchTerm}%");
                        });
                    case 'address':
                        return $query->where(function($q) use ($searchTerm) {
                            $q->where('address', 'like', "%{$searchTerm}%")
                              ->orWhere('address_line2', 'like', "%{$searchTerm}%")
                              ->orWhere('address_line3', 'like', "%{$searchTerm}%");
                        });
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('code', 'like', "%{$searchTerm}%")
                      ->orWhere('mobile', 'like', "%{$searchTerm}%")
                      ->orWhere('telephone_office', 'like', "%{$searchTerm}%")
                      ->orWhere('telephone_residence', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%")
                      ->orWhere('address_line2', 'like', "%{$searchTerm}%")
                      ->orWhere('address_line3', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                $fullAddress = collect([$item->address, $item->address_line2, $item->address_line3])
                    ->filter()
                    ->implode(', ');
                    
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code ?? '-',
                    'ledger_type' => 'Customer',
                    'debit' => $item->balance_type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->balance_type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => $fullAddress ?: '-',
                    'mobile' => $item->mobile ?? '-',
                    'email' => $item->email ?? '-',
                    'telephone' => $item->telephone_office ?? $item->telephone_residence ?? '-',
                    'fax' => $item->fax_number ?? '-',
                    'contact_person1' => $item->contact_person1 ?? '-',
                    'contact_person2' => $item->contact_person2 ?? '-',
                ];
            });
        }

        // Fetch Suppliers if no ledger type filter or if Supplier is selected
        if (!$ledgerType || $ledgerType === 'Supplier') {
            $suppliers = Supplier::select('supplier_id', 'name', 'code', 'opening_balance', 'opening_balance_type', 'address', 'mobile', 'mobile_additional', 'email', 'telephone', 'fax', 'status', 'contact_person_1', 'contact_person_2', 'tan_no', 'msme_lic', 'gst_no', 'pan')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('code', 'like', "%{$searchTerm}%");
                    case 'mobile':
                        return $query->where('mobile', 'like', "%{$searchTerm}%");
                    case 'telephone':
                        return $query->where('telephone', 'like', "%{$searchTerm}%");
                    case 'address':
                        return $query->where('address', 'like', "%{$searchTerm}%");
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('code', 'like', "%{$searchTerm}%")
                      ->orWhere('mobile', 'like', "%{$searchTerm}%")
                      ->orWhere('telephone', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->supplier_id,
                    'name' => $item->name,
                    'code' => $item->code ?? '-',
                    'ledger_type' => 'Supplier',
                    'debit' => $item->opening_balance_type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->opening_balance_type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => $item->address ?? '-',
                    'mobile' => $item->mobile ?? '-',
                    'email' => $item->email ?? '-',
                    'telephone' => $item->telephone ?? '-',
                    'fax' => $item->fax ?? '-',
                    'contact_person1' => $item->contact_person_1 ?? '-',
                    'contact_person2' => $item->contact_person_2 ?? '-',
                    'status' => $item->status ?? '-',
                    'mobile_additional' => $item->mobile_additional ?? '-',
                ];
            });
        }

        // Fetch General Ledger if no ledger type filter or if General Ledger is selected
        if (!$ledgerType || $ledgerType === 'General Ledger') {
            $generalLedgers = GeneralLedger::select('id', 'account_name', 'account_code', 'opening_balance', 'balance_type')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('account_name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('account_code', 'like', "%{$searchTerm}%");
                    // Skip mobile, telephone, address for General Ledger as columns don't exist
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('account_name', 'like', "%{$searchTerm}%")
                      ->orWhere('account_code', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->account_name,
                    'code' => $item->account_code ?? '-',
                    'ledger_type' => 'General Ledger',
                    'debit' => $item->balance_type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->balance_type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => '-',
                    'mobile' => '-',
                    'email' => '-',
                    'telephone' => '-',
                    'fax' => '-',
                    'contact_person1' => '-',
                    'contact_person2' => '-',
                ];
            });
        }

        // Fetch Cash/Bank Books if no ledger type filter or if Cash / Bank is selected
        if (!$ledgerType || $ledgerType === 'Cash / Bank') {
            $cashBankBooks = CashBankBook::select('id', 'name', 'alter_code', 'opening_balance', 'opening_balance_type', 'address', 'address1', 'account_no', 'telephone', 'email', 'fax', 'mobile_1', 'mobile_2', 'contact_person_1', 'contact_person_2')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('alter_code', 'like', "%{$searchTerm}%");
                    case 'mobile':
                        return $query->where(function($q) use ($searchTerm) {
                            $q->where('mobile_1', 'like', "%{$searchTerm}%")
                              ->orWhere('mobile_2', 'like', "%{$searchTerm}%");
                        });
                    case 'telephone':
                        return $query->where('telephone', 'like', "%{$searchTerm}%");
                    case 'address':
                        return $query->where(function($q) use ($searchTerm) {
                            $q->where('address', 'like', "%{$searchTerm}%")
                              ->orWhere('address1', 'like', "%{$searchTerm}%");
                        });
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('alter_code', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%")
                      ->orWhere('address1', 'like', "%{$searchTerm}%")
                      ->orWhere('account_no', 'like', "%{$searchTerm}%")
                      ->orWhere('telephone', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('mobile_1', 'like', "%{$searchTerm}%")
                      ->orWhere('mobile_2', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                $fullAddress = collect([$item->address, $item->address1])
                    ->filter()
                    ->implode(', ');
                    
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->alter_code ?? '-',
                    'ledger_type' => 'Cash / Bank',
                    'debit' => $item->opening_balance_type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->opening_balance_type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => $fullAddress ?: '-',
                    'mobile' => $item->mobile_1 ?? '-',
                    'email' => $item->email ?? '-',
                    'telephone' => $item->telephone ?? '-',
                    'fax' => $item->fax ?? '-',
                    'contact_person1' => $item->contact_person_1 ?? '-',
                    'contact_person2' => $item->contact_person_2 ?? '-',
                    'account_no' => $item->account_no ?? '-',
                ];
            });
        }

        // Fetch Sale Ledger if no ledger type filter or if Sale Ledger is selected
        if (!$ledgerType || $ledgerType === 'Sale Ledger') {
            $saleLedgers = SaleLedger::select('id', 'ledger_name', 'alter_code', 'opening_balance', 'type')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('ledger_name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('alter_code', 'like', "%{$searchTerm}%");
                    // Skip mobile, telephone, address for Sale Ledger as columns may not exist
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('ledger_name', 'like', "%{$searchTerm}%")
                      ->orWhere('alter_code', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ledger_name,
                    'code' => $item->alter_code ?? '-',
                    'ledger_type' => 'Sale Ledger',
                    'debit' => $item->type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => '-',
                    'mobile' => '-',
                    'email' => '-',
                    'telephone' => '-',
                    'fax' => '-',
                    'contact_person1' => '-',
                    'contact_person2' => '-',
                ];
            });
        }

        // Fetch Purchase Ledger if no ledger type filter or if Purchase Ledger is selected
        if (!$ledgerType || $ledgerType === 'Purchase Ledger') {
            $purchaseLedgers = PurchaseLedger::select('id', 'ledger_name', 'alter_code', 'opening_balance', 'type')
            ->when($searchTerm && $searchField !== 'all', function ($query) use ($searchField, $searchTerm) {
                switch ($searchField) {
                    case 'split_name':
                        return $query->where('ledger_name', 'like', "%{$searchTerm}%");
                    case 'alter_code':
                        return $query->where('alter_code', 'like', "%{$searchTerm}%");
                    // Skip mobile, telephone, address for Purchase Ledger as columns may not exist
                }
            })
            ->when($searchTerm && $searchField === 'all', function ($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->where('ledger_name', 'like', "%{$searchTerm}%")
                      ->orWhere('alter_code', 'like', "%{$searchTerm}%");
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ledger_name,
                    'code' => $item->alter_code ?? '-',
                    'ledger_type' => 'Purchase Ledger',
                    'debit' => $item->type === 'D' ? ($item->opening_balance ?? 0) : 0,
                    'credit' => $item->type === 'C' ? ($item->opening_balance ?? 0) : 0,
                    'address' => '-',
                    'mobile' => '-',
                    'email' => '-',
                    'telephone' => '-',
                    'fax' => '-',
                    'contact_person1' => '-',
                    'contact_person2' => '-',
                ];
            });
        }

        // Combine all ledgers
        $allLedgers = collect()
            ->merge($customers)
            ->merge($suppliers)
            ->merge($generalLedgers)
            ->merge($cashBankBooks)
            ->merge($saleLedgers)
            ->merge($purchaseLedgers)
            ->sortBy('name')
            ->values();

        // Calculate totals before pagination
        $totalDebit = $allLedgers->sum('debit');
        $totalCredit = $allLedgers->sum('credit');
        $totalCount = $allLedgers->count();

        // Implement pagination
        $total = $allLedgers->count();
        $items = $allLedgers->forPage($page, $perPage);
        
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        // For AJAX requests, return only the table content
        if ($request->ajax()) {
            return view('admin.all-ledger.index', compact('paginator', 'totalDebit', 'totalCredit', 'totalCount'))->render();
        }

        return view('admin.all-ledger.index', compact('paginator', 'totalDebit', 'totalCredit', 'totalCount'));
    }

    public function getLedgerDetails(Request $request)
    {
        $ledgerType = $request->get('ledger_type');
        $ledgerId = $request->get('ledger_id');

        // Debug logging
        \Log::info('Ledger Details Request:', [
            'ledger_type' => $ledgerType,
            'ledger_id' => $ledgerId
        ]);

        $details = [];

        switch ($ledgerType) {
            case 'Customer':
                $customer = Customer::find($ledgerId);
                if ($customer) {
                    $fullAddress = collect([$customer->address, $customer->address_line2, $customer->address_line3])
                        ->filter()
                        ->implode(', ');
                    
                    $details = [
                        'basic_info' => [
                            'name' => $customer->name,
                            'code' => $customer->code ?? '-',
                            'type' => 'Customer',
                            'address' => $fullAddress ?: '-',
                            'city' => $customer->city ?? '-',
                            'pin_code' => $customer->pin_code ?? '-',
                            'state' => $customer->state_name ?? '-',
                            'country' => $customer->country_name ?? '-',
                        ],
                        'contact_info' => [
                            'mobile' => $customer->mobile ?? '-',
                            'telephone_office' => $customer->telephone_office ?? '-',
                            'telephone_residence' => $customer->telephone_residence ?? '-',
                            'email' => $customer->email ?? '-',
                            'fax' => $customer->fax_number ?? '-',
                            'contact_person1' => $customer->contact_person1 ?? '-',
                            'mobile_contact1' => $customer->mobile_contact1 ?? '-',
                            'contact_person2' => $customer->contact_person2 ?? '-',
                            'mobile_contact2' => $customer->mobile_contact2 ?? '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $customer->opening_balance ?? 0,
                            'balance_type' => $customer->balance_type ?? 'C',
                            'debit' => $customer->balance_type === 'D' ? ($customer->opening_balance ?? 0) : 0,
                            'credit' => $customer->balance_type === 'C' ? ($customer->opening_balance ?? 0) : 0,
                        ],
                        'additional_info' => [
                            'gst_number' => $customer->gst_number ?? '-',
                            'pan_number' => $customer->pan_number ?? '-',
                            'aadhar_number' => $customer->aadhar_number ?? '-',
                            'registration_date' => $customer->registration_date ?? '-',
                            'credit_limit' => $customer->credit_limit ?? 0,
                        ]
                    ];
                }
                break;

            case 'Supplier':
                $supplier = Supplier::where('supplier_id', $ledgerId)->first();
                \Log::info('Supplier lookup result:', ['supplier' => $supplier ? 'found' : 'not found', 'id' => $ledgerId]);
                if ($supplier) {
                    $details = [
                        'basic_info' => [
                            'name' => $supplier->name,
                            'code' => $supplier->code ?? '-',
                            'type' => 'Supplier',
                            'address' => $supplier->address ?? '-',
                            'status' => $supplier->status ?? '-',
                        ],
                        'contact_info' => [
                            'mobile' => $supplier->mobile ?? '-',
                            'mobile_additional' => $supplier->mobile_additional ?? '-',
                            'telephone' => $supplier->telephone ?? '-',
                            'email' => $supplier->email ?? '-',
                            'fax' => $supplier->fax ?? '-',
                            'contact_person1' => $supplier->contact_person_1 ?? '-',
                            'contact_person2' => $supplier->contact_person_2 ?? '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $supplier->opening_balance ?? 0,
                            'balance_type' => $supplier->opening_balance_type ?? 'C',
                            'debit' => $supplier->opening_balance_type === 'D' ? ($supplier->opening_balance ?? 0) : 0,
                            'credit' => $supplier->opening_balance_type === 'C' ? ($supplier->opening_balance ?? 0) : 0,
                        ],
                        'additional_info' => [
                            'tan_number' => $supplier->tan_no ?? '-',
                            'msme_license' => $supplier->msme_lic ?? '-',
                            'gst_number' => $supplier->gst_no ?? '-',
                            'pan_number' => $supplier->pan ?? '-',
                            'credit_limit' => $supplier->credit_limit ?? 0,
                        ]
                    ];
                }
                break;

            case 'Cash / Bank':
                $cashBank = CashBankBook::find($ledgerId);
                if ($cashBank) {
                    $fullAddress = collect([$cashBank->address, $cashBank->address1])
                        ->filter()
                        ->implode(', ');
                    
                    $details = [
                        'basic_info' => [
                            'name' => $cashBank->name,
                            'code' => $cashBank->alter_code ?? '-',
                            'type' => 'Cash / Bank',
                            'address' => $fullAddress ?: '-',
                        ],
                        'contact_info' => [
                            'mobile' => $cashBank->mobile_1 ?? '-',
                            'telephone' => $cashBank->telephone ?? '-',
                            'email' => $cashBank->email ?? '-',
                            'fax' => $cashBank->fax ?? '-',
                            'contact_person1' => $cashBank->contact_person_1 ?? '-',
                            'contact_person2' => $cashBank->contact_person_2 ?? '-',
                            'mobile_contact1' => $cashBank->mobile_1 ?? '-',
                            'mobile_contact2' => $cashBank->mobile_2 ?? '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $cashBank->opening_balance ?? 0,
                            'balance_type' => $cashBank->opening_balance_type ?? 'C',
                            'debit' => $cashBank->opening_balance_type === 'D' ? ($cashBank->opening_balance ?? 0) : 0,
                            'credit' => $cashBank->opening_balance_type === 'C' ? ($cashBank->opening_balance ?? 0) : 0,
                        ],
                        'bank_details' => [
                            'account_number' => $cashBank->account_no ?? '-',
                            'bank_charges' => $cashBank->bank_charges ?? 0,
                            'credit_card' => $cashBank->credit_card ?? '-',
                            'cheque_clearance_method' => $cashBank->cheque_clearance_method ?? '-',
                        ]
                    ];
                }
                break;

            case 'General Ledger':
                $generalLedger = GeneralLedger::find($ledgerId);
                if ($generalLedger) {
                    $details = [
                        'basic_info' => [
                            'name' => $generalLedger->account_name,
                            'code' => $generalLedger->account_code ?? '-',
                            'type' => 'General Ledger',
                            'address' => '-',
                        ],
                        'contact_info' => [
                            'mobile' => '-',
                            'telephone' => '-',
                            'email' => '-',
                            'fax' => '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $generalLedger->opening_balance ?? 0,
                            'balance_type' => $generalLedger->balance_type ?? 'C',
                            'debit' => $generalLedger->balance_type === 'D' ? ($generalLedger->opening_balance ?? 0) : 0,
                            'credit' => $generalLedger->balance_type === 'C' ? ($generalLedger->opening_balance ?? 0) : 0,
                        ],
                        'additional_info' => [
                            'account_group' => $generalLedger->account_group ?? '-',
                            'description' => $generalLedger->description ?? '-',
                        ]
                    ];
                }
                break;

            case 'Sale Ledger':
                $saleLedger = SaleLedger::find($ledgerId);
                if ($saleLedger) {
                    $details = [
                        'basic_info' => [
                            'name' => $saleLedger->ledger_name,
                            'code' => $saleLedger->alter_code ?? '-',
                            'type' => 'Sale Ledger',
                            'address' => $saleLedger->address ?? '-',
                        ],
                        'contact_info' => [
                            'mobile' => $saleLedger->mobile_1 ?? '-',
                            'telephone' => $saleLedger->telephone ?? '-',
                            'email' => $saleLedger->email ?? '-',
                            'fax' => $saleLedger->fax ?? '-',
                            'contact_person1' => $saleLedger->contact_1 ?? '-',
                            'contact_person2' => $saleLedger->contact_2 ?? '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $saleLedger->opening_balance ?? 0,
                            'balance_type' => $saleLedger->type ?? 'C',
                            'debit' => $saleLedger->type === 'D' ? ($saleLedger->opening_balance ?? 0) : 0,
                            'credit' => $saleLedger->type === 'C' ? ($saleLedger->opening_balance ?? 0) : 0,
                        ],
                        'additional_info' => [
                            'form_type' => $saleLedger->form_type ?? '-',
                            'sale_tax' => $saleLedger->sale_tax ?? 0,
                            'charges' => $saleLedger->charges ?? 0,
                        ]
                    ];
                }
                break;

            case 'Purchase Ledger':
                $purchaseLedger = PurchaseLedger::find($ledgerId);
                if ($purchaseLedger) {
                    $details = [
                        'basic_info' => [
                            'name' => $purchaseLedger->ledger_name,
                            'code' => $purchaseLedger->alter_code ?? '-',
                            'type' => 'Purchase Ledger',
                            'address' => $purchaseLedger->address ?? '-',
                        ],
                        'contact_info' => [
                            'mobile' => $purchaseLedger->mobile_1 ?? '-',
                            'telephone' => $purchaseLedger->telephone ?? '-',
                            'email' => $purchaseLedger->email ?? '-',
                            'fax' => $purchaseLedger->fax ?? '-',
                            'contact_person1' => $purchaseLedger->contact_1 ?? '-',
                            'contact_person2' => $purchaseLedger->contact_2 ?? '-',
                        ],
                        'balance_info' => [
                            'opening_balance' => $purchaseLedger->opening_balance ?? 0,
                            'balance_type' => $purchaseLedger->type ?? 'C',
                            'debit' => $purchaseLedger->type === 'D' ? ($purchaseLedger->opening_balance ?? 0) : 0,
                            'credit' => $purchaseLedger->type === 'C' ? ($purchaseLedger->opening_balance ?? 0) : 0,
                        ],
                        'additional_info' => [
                            'form_type' => $purchaseLedger->form_type ?? '-',
                            'sale_tax' => $purchaseLedger->sale_tax ?? 0,
                            'charges' => $purchaseLedger->charges ?? 0,
                        ]
                    ];
                }
                break;
        }

        if (empty($details)) {
            return response()->json([
                'success' => false,
                'message' => 'Ledger details not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }
}
