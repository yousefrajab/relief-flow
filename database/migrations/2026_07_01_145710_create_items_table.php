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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // اسم المادة الإغاثية (مثال: سلة غذائية عائلية)
            $table->string('category');     // تصنيف المادة (مثال: Food, Medical, Shelter, Hygiene)
            $table->string('unit');         // وحدة القياس (مثال: box, kit, liter, kg)
            $table->text('description')->nullable(); // وصف اختياري وتفصيلي لمحتويات المادة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
