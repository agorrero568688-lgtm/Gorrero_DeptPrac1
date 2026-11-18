<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for the plants inventory table.
     * Links each plant record to the user who created it via user_id foreign key.
     */
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            
            // Foreign key linking each plant to the administrator (User) who created it
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Required Plant CRUD fields per SET D specification
            $table->string('plant_name');
            $table->string('category'); // e.g., Ornamental, Flowering, Succulent, Herb
            $table->decimal('unit_price', 10, 2); // Numeric and nonnegative
            $table->text('description');
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};