<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

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
                $width = (float) ($item['width'] ?? 0);
                $length = (float) ($item['length'] ?? $item['height'] ?? 0);
                $height = (float) ($item['height'] ?? $item['length'] ?? 0);
                $quantity = (float) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $area = round($length * $width / 10000, 3);
                $metraj = $area > 0 ? round(max($area, 0.8) * $quantity, 3) : 0;
                $lineTotal = round($metraj * $unitPrice, 3);

                $productId = isset($item['product_id']) ? (int) $item['product_id'] : null;
                $description = $item['description'] ?? null;

                if ($productId !== null && empty($description)) {
                    $product = Product::find($productId);
                    $description = $product ? $product->title : null;
                }

                return [
                    'row' => isset($item['row']) ? (int) $item['row'] : null,
                    'product_id' => $productId,
                    'order_type' => $item['order_type'] ?? null,
                    'description' => $description,
                    'width' => $width,
                    'length' => $length,
                    'height' => $height,
                    'quantity' => $quantity,
                    'area' => $area,
                    'meterage' => $metraj,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                    'line_total' => $lineTotal,
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
