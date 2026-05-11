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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Unique Ticket ID (Format: CK00000001)
            $table->string('ticket_id')->unique(); 
            
            // Category to distinguish registration from general app issues
            $table->enum('category', ['registration', 'app_issue'])->default('app_issue');
            
            // Main Content
            $table->string('subject');
            $table->text('message');
            
            /* registration_data: Stores Name, Nickname, Num, and Hashed Password 
               in JSON format until the Developer approves it.
            */
            $table->json('registration_data')->nullable(); 
            
            // Ticket Lifecycle Status
            $table->enum('status', ['Open', 'Progress', 'Closed'])->default('Open');
            
            // Optional: Track which Admin/User sent the ticket
            $table->string('user_num')->nullable(); // Reference phone
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};