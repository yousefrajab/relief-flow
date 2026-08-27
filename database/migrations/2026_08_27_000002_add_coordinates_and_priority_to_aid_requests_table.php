<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aid_requests', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('priority')->default('normal')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('aid_requests', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'priority']);
        });
    }
};
