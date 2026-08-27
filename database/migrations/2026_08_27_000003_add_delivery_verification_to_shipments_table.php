<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('delivery_photo_path')->nullable()->after('delivered_at');
            $table->string('ai_verification_status')->nullable()->after('delivery_photo_path');
            $table->text('ai_verification_notes')->nullable()->after('ai_verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['delivery_photo_path', 'ai_verification_status', 'ai_verification_notes']);
        });
    }
};
