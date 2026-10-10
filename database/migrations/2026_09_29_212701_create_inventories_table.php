<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            /*
             * One inventory record per product.
             */
            $table->foreignId('product_id')
                ->unique()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Physical/system stock currently on hand.
             *
             * Reserved stock is NOT deducted here.
             */
            $table->integer('current_stock')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};