<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'date' => 'nullable|string|max:50',
            'items' => 'required|array',
            'shipping_cost' => 'nullable|numeric',
            'installation_cost' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);

        $invoice = Invoice::create([
            'customer_name' => $validated['customer_name'] ?? '',
            'date' => $validated['date'] ?? '',
            'items' => json_encode($validated['items']),
            'shipping_cost' => $validated['shipping_cost'] ?? 0,
            'installation_cost' => $validated['installation_cost'] ?? 0,
            'grand_total' => $validated['grand_total'] ?? 0,
        ]);

        return response()->json(['success' => true, 'id' => $invoice->id]);
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        return response()->json($invoice);
    }

    public function index()
    {
        return Invoice::orderBy('id', 'desc')->get();
    }
}
