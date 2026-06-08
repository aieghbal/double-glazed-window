<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_name',
        'date',
        'items',
        'shipping_cost',
        'installation_cost',
        'grand_total',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping_cost' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice): void {
            $items = static::normalizeItems($invoice->items ?? []);

            $invoice->items = $items;
            $invoice->grand_total = static::calculateGrandTotal([
                'items' => $items,
                'shipping_cost' => $invoice->shipping_cost,
                'installation_cost' => $invoice->installation_cost,
            ]);
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(function (array $item): array {
                $quantity = (float) ($item['quantity'] ?? 0);
                $length = (float) ($item['length'] ?? 0);
                $width = (float) ($item['width'] ?? 0);
                $area = round($length * $width, 3);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                return [
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'length' => $length,
                    'width' => $width,
                    'area' => $area,
                    'unit_price' => $unitPrice,
                    'line_total' => round($quantity * $area * $unitPrice, 3),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function calculateGrandTotal(array $data): float
    {
        $items = static::normalizeItems($data['items'] ?? []);
        $subtotal = collect($items)->sum(fn (array $item): float => (float) ($item['line_total'] ?? 0));

        return round(
            $subtotal
            + (float) ($data['shipping_cost'] ?? 0)
            + (float) ($data['installation_cost'] ?? 0),
            2,
        );
    }
}
