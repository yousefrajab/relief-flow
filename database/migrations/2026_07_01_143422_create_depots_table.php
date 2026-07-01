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
        Schema::create('depots', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المستودع (مثال: مستودع غزة المركزي)
            $table->string('location'); // موقع المستودع الجغرافي (مثال: دير البلح)
            $table->integer('capacity')->nullable(); // السعة التخزينية القصوى بالطن أو الصناديق
            $table->boolean('is_active')->default(true); // حالة المستودع (نشط أم متوقف)
            $table->timestamps(); // تاريخ ووقت الإنشاء والتحديث تلقائياً
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
