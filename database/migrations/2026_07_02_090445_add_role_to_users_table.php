<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل هجرة إضافة حقل الأدوار
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل الأدوار ويكون المنسق هو الافتراضي (admin, depot_manager, coordinator)
            $table->string('role')->default('coordinator')->after('password');
        });
    }

    /**
     * تراجع عن التعديل
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};