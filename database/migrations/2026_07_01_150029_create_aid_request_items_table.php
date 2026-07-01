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
        Schema::create('aid_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aid_request_id')->constrained()->onDelete('cascade'); // ربط بالطلب الأساسي
            $table->foreignId('item_id')->constrained()->onDelete('cascade');        // ربط بالمادة المطلوبة
            $table->integer('quantity');                                            // الكمية المطلوبة من هذه المادة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_request_items');
    }
};
