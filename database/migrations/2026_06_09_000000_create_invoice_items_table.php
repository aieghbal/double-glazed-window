<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedSmallInteger('row')->nullable();
            $table->string('order_type')->nullable();
            $table->string('description')->nullable();
            $table->decimal('width', 15, 4)->default(0);
            $table->decimal('length', 15, 4)->default(0);
            $table->decimal('height', 15, 4)->default(0);
            $table->decimal('area', 15, 4)->default(0);
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('meterage', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
