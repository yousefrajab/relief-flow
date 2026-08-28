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
        Schema::table('shipments', function (Blueprint $table) {
            $table->timestamp('picked_up_at')->nullable()->after('warehouse_id');
            $table->string('pickup_photo_path')->nullable()->after('picked_up_at');
            $table->string('pickup_ai_verification_status')->nullable()->after('pickup_photo_path');
            $table->text('pickup_ai_verification_notes')->nullable()->after('pickup_ai_verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'pickup_photo_path', 'pickup_ai_verification_status', 'pickup_ai_verification_notes']);
        });
    }
};
