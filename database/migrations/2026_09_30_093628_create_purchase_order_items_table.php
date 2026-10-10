<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Quantity ordered from supplier.
             */
            $table->integer('quantity');

            /*
             * Quantity actually received from supplier.
             *
             * This changes only through the Stock In/receiving process.
             */
            $table->integer('received_quantity')
                ->default(0);

            /*
             * Actual supplier purchase cost for this PO.
             *
             * This is intentionally NOT stored in products.unit_price.
             */
            $table->decimal('unit_cost', 12, 2);

            /*
             * quantity × unit_cost
             */
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};