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
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.order_type' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.width' => 'nullable|numeric',
            'items.*.height' => 'nullable|numeric',
            'items.*.length' => 'nullable|numeric',
            'items.*.quantity' => 'nullable|numeric',
            'items.*.meterage' => 'nullable|numeric',
            'items.*.unit_price' => 'nullable|numeric',
            'items.*.total_price' => 'nullable|numeric',
            'items.*.line_total' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'installation_cost' => 'nullable|numeric',
        ]);

        $items = collect($validated['items'])
            ->filter(function (array $item): bool {
                return !empty(trim($item['order_type'] ?? ''))
                    || !empty(trim($item['description'] ?? ''))
                    || isset($item['product_id'])
                    || (float) ($item['width'] ?? 0) > 0
                    || (float) ($item['height'] ?? 0) > 0
                    || (float) ($item['length'] ?? 0) > 0
                    || (float) ($item['quantity'] ?? 0) > 0
                    || (float) ($item['unit_price'] ?? 0) > 0;
            })
            ->values()
            ->all();

        $invoice = Invoice::create([
            'customer_name' => $validated['customer_name'] ?? '',
            'date' => $validated['date'] ?? '',
            'items' => $items,
            'shipping_cost' => $validated['shipping_cost'] ?? 0,
            'installation_cost' => $validated['installation_cost'] ?? 0,
        ]);

        if (! empty($items)) {
            $invoice->lineItems()->createMany($items);
        }

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
