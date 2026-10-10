<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('supplier_name');

            $table->string('contact_person');

            $table->string('contact_number');

            $table->string('email')
                ->nullable();

            $table->text('address')
                ->nullable();

            /*
             * active   : available for new Purchase Orders
             * inactive : hidden from the active list, restorable
             * archived : hidden from the active list, restorable
             *
             * A supplier is never deleted. Historical POs keep it.
             */
            $table->enum('status', [
                'active',
                'inactive',
                'archived',
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};