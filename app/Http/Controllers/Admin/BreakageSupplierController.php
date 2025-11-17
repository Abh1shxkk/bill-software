<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BreakageSupplierController extends Controller
{
    /**
     * Display issued transaction form
     */
    public function issuedTransaction()
    {
        return view('admin.breakage-supplier.issued-transaction');
    }

    /**
     * Display issued modification form
     */
    public function issuedModification()
    {
        return view('admin.breakage-supplier.issued-modification');
    }

    /**
     * Display received transaction form
     */
    public function receivedTransaction()
    {
        return view('admin.breakage-supplier.received-transaction');
    }

    /**
     * Display received modification form
     */
    public function receivedModification()
    {
        return view('admin.breakage-supplier.received-modification');
    }

    /**
     * Display unused dump transaction form
     */
    public function unusedDumpTransaction()
    {
        return view('admin.breakage-supplier.unused-dump-transaction');
    }

    /**
     * Display unused dump modification form
     */
    public function unusedDumpModification()
    {
        return view('admin.breakage-supplier.unused-dump-modification');
    }
}
