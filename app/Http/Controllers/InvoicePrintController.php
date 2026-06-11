<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicePrintController extends Controller
{
    public function show(Invoice $invoice)
    {
        return view('invoice.print', [
            'invoice' => $invoice,
        ]);
    }
}
