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
        Schema::create('transaction_details', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('transaction_id');
        $table->string('item_type'); // Stores 'service' or 'inventory'
        $table->unsignedBigInteger('item_id'); // Stores the ID of the Service or Inventory item
        $table->integer('quantity')->default(1);
        $table->decimal('price_at_time', 15, 2); // To lock in the price at the moment of sale

        $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
