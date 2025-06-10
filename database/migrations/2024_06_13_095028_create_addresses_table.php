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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country');
            $table->string('email');
            $table->string('phone');

            $table->foreignId('sale_id')->nullable()
                ->constrained('sales')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
