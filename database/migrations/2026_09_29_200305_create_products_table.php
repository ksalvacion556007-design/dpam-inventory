<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('product_name');

            /*
             * Brand belongs to the product.
             *
             * Supplier relationship is handled separately.
             * A product may be supplied by multiple suppliers.
             */
            $table->string('brand')->nullable();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('api')->nullable();

            $table->string('base_oil')->nullable();

            $table->string('unit');

            $table->string('package_size')->nullable();

            /*
             * Customer selling price.
             *
             * Supplier purchase cost is NOT stored here.
             * Actual supplier purchase cost belongs to PO items.
             */
            $table->decimal('unit_price', 12, 2)
                ->default(0);

            /*
             * Product-level reorder threshold.
             */
            $table->integer('reorder_level')
                ->default(0);

            /*
             * Inactive products remain in the database and history,
             * but should not normally appear in active product selection.
             */
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};