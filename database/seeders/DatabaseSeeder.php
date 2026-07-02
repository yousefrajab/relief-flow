<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * تغذية قاعدة البيانات بالبيانات والمستخدمين الافتراضيين
     */
    public function run(): void
    {
        // 1. مسح المستخدمين القدامى وتجهيز المستخدمين الثلاثة بأدوارهم وصلاحياتهم
        User::whereIn('email', [
            'admin@reliefflow.com',
            'manager@reliefflow.com',
            'coordinator@reliefflow.com'
        ])->delete();

        // الآدمن (له كامل الصلاحيات)
        User::create([
            'name' => 'Yousef Al Khateeb (Admin)',
            'email' => 'admin@reliefflow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // أمين المستودع (لإدارة المخزون والشحن)
        User::create([
            'name' => 'Mahmoud Depot Manager',
            'email' => 'manager@reliefflow.com',
            'password' => Hash::make('password'),
            'role' => 'depot_manager',
        ]);

        // منسق الميدان (لطلب المساعدات وتأكيد الاستلام)
        User::create([
            'name' => 'Ahmad Field Coordinator',
            'email' => 'coordinator@reliefflow.com',
            'password' => Hash::make('password'),
            'role' => 'coordinator',
        ]);

        // 2. تصفير وتحديث المخازن التجريبية
        Warehouse::truncate();
        Warehouse::create([
            'name' => 'Central Gaza Depot',
            'location' => 'Deir El-Balah, Salah Al-Din Road',
            'capacity' => 10000,
            'status' => 'active'
        ]);

        Warehouse::create([
            'name' => 'South Gaza Facility',
            'location' => 'Rafah, Al-Salam Neighborhood',
            'capacity' => 15000,
            'status' => 'active'
        ]);

        Warehouse::create([
            'name' => 'North Gaza Hub',
            'location' => 'Jabalia Al-Balad',
            'capacity' => 5000,
            'status' => 'inactive'
        ]);

        // 3. تصفير وتحديث المواد الإغاثية
        Item::truncate();
        Item::create([
            'name' => 'Family Food Parcel',
            'category' => 'Food',
            'unit' => 'box',
            'description' => 'Contains sugar, rice, cooking oil, lentils, and canned goods.'
        ]);

        Item::create([
            'name' => 'Hygiene Package',
            'category' => 'Hygiene',
            'unit' => 'kit',
            'description' => 'Includes soap, toothpaste, toothbrushes, and towels.'
        ]);

        Item::create([
            'name' => 'Emergency Medical Kit',
            'category' => 'Medical',
            'unit' => 'kit',
            'description' => 'Essential surgical bandages and first-aid tools.'
        ]);

        Item::create([
            'name' => 'Wheat Flour Sack (25kg)',
            'category' => 'Food',
            'unit' => 'bag',
            'description' => 'High-grade baking flour for local community kitchens.'
        ]);
        
        // 4. تصفير عمليات الجرد السابقة لضمان سلامة قاعدة البيانات
        Inventory::truncate();
    }
}