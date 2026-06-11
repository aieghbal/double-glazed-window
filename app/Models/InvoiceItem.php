<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'row',
        'order_type',
        'description',
        'width',
        'length',
        'height',
        'area',
        'quantity',
        'meterage',
        'unit_price',
        'total_price',
        'line_total',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'width' => 'decimal:4',
        'length' => 'decimal:4',
        'height' => 'decimal:4',
        'area' => 'decimal:4',
        'quantity' => 'decimal:4',
        'meterage' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
