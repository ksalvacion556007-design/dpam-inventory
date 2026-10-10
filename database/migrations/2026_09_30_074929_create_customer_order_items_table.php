<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_order_id')
                ->constrained('customer_orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Total quantity ordered.
             */
            $table->integer('quantity');

            /*
             * Quantity currently reserved for this order.
             *
             * Reservation does not reduce current_stock.
             * It reduces AVAILABLE stock.
             */
            $table->integer('reserved_quantity')
                ->default(0);

            /*
             * Quantity already physically released/delivered.
             */
            $table->integer('fulfilled_quantity')
                ->default(0);

            /*
             * Selling price captured at the time of order.
             */
            $table->decimal('unit_price', 12, 2);

            /*
             * quantity × unit_price
             */
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_order_items');
    }
};