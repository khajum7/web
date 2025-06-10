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
        Schema::create('sales', function (Blueprint $table) {
            $table->id()->from(10000001);

            $table->date('delivery_date');
            $table->date('program_start_date');
            $table->string('organization_name');
            $table->text('approvalNote')->nullable();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('warehouse_order_id')->nullable();
            $table->text('warehouse_message')->nullable();

            $table->enum('status', array_keys(StatusConstant::$orderStatus))->default(StatusConstant::ORDER_PLACED)->comment('1: order placed, 2: order shipped, 3: order canceled');
            $table->enum('approval_status', array_keys(StatusConstant::$approvedStatus))->default(StatusConstant::NEW_ORDER)->comment('0:New Order, 1: Approved, 2: unapproved');

            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
