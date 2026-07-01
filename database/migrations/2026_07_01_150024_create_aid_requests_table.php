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
        Schema::create('aid_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // منسق الميدان الذي أنشأ الطلب
            $table->string('location');                                      // موقع التوزيع المستهدف
            $table->text('notes')->nullable();                               // ملاحظات إضافية على الطلب
            $table->string('status')->default('pending');                    // حالة الطلب (pending, approved, rejected, dispatched, delivered)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_requests');
    }
};
