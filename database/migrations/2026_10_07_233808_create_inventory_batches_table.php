<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SUPPLIER
            |--------------------------------------------------------------------------
            | The supplier that actually delivered this batch.
            | Historical batches remain even if the supplier later becomes
            | inactive or archived.
            */
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PURCHASE ORDER
            |--------------------------------------------------------------------------
            | Optional because not every inventory batch necessarily needs to
            | originate from a Purchase Order.
            */
            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('purchase_orders')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | BATCH / LOT INFORMATION
            |--------------------------------------------------------------------------
            */
            $table->string('batch_number', 100);

            $table->date('received_date');

            /*
            |--------------------------------------------------------------------------
            | EXPIRATION / BEST BEFORE
            |--------------------------------------------------------------------------
            | Both are optional because not every industrial product has
            | an expiration or best-before date.
            */
            $table->date('expiration_date')->nullable();

            $table->date('best_before_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | QUANTITY
            |--------------------------------------------------------------------------
            */
            $table->integer('quantity_received')->default(0);

            $table->integer('quantity_remaining')->default(0);

            /*
            |--------------------------------------------------------------------------
            | PURCHASE COST
            |--------------------------------------------------------------------------
            | This is supplier purchase cost, NOT the product selling price.
            */
            $table->decimal('unit_cost', 12, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'active',
                'depleted',
                'expired',
            ])->default('active');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE BATCH NUMBER PER PRODUCT
            |--------------------------------------------------------------------------
            */
            $table->unique([
                'product_id',
                'batch_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};