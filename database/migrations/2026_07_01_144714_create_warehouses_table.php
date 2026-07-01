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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // اسم المخزن (مثال: مستودع غزة المركزي)
            $table->string('location');         // موقع المخزن بالتفصيل (مثال: دير البلح)
            $table->integer('capacity')->nullable(); // السعة الاستيعابية القصوى (عدد الوجبات أو الطرود)
            $table->string('status')->default('active'); // حالة المخزن (نشط active أو غير نشط inactive)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
