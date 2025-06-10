<?php

use App\Constant\StatusConstant;
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
        Schema::table('product_items', function (Blueprint $table) {
            $table->enum('type', array_keys(StatusConstant::$productType))
                ->default(StatusConstant::PRODUCT_TYPE_UNDEFINED)
                ->comment('0: undefined product type, 1: product type 10 set, 2: product type 8 set, 3: jersey individual product type, 4: shorts product type')
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
