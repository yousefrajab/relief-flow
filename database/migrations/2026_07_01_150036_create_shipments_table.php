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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aid_request_id')->constrained()->onDelete('cascade'); // ربط بالطلب الميداني
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');   // المستودع الذي خرجت منه الشحنة
            $table->string('driver_name')->nullable();                               // اسم السائق
            $table->string('driver_phone')->nullable();                              // هاتف السائق
            $table->string('status')->default('dispatched');                         // حالة الشحنة (dispatched خرجت، delivered استلمت)
            $table->string('qr_code_token')->unique();                               // رمز الـ QR الفريد لتأكيد الاستلام ميدانياً
            $table->timestamp('delivered_at')->nullable();                           // وقت وتاريخ الاستلام الفعلي في الميدان
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
