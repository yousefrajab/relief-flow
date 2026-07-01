<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إضافة مخازن تجريبية واقعية تحاكي الجغرافيا الفعلية
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
            'status' => 'inactive' // سنستخدمه لمحاكاة مستودع متوقف مؤقتاً في الكود لاحقاً
        ]);

        // 2. إضافة مواد إغاثية حقيقية بمواصفات دقيقة
        Item::create([
            'name' => 'Family Food Parcel',
            'category' => 'Food',
            'unit' => 'box',
            'description' => 'Contains sugar, rice, cooking oil, lentils, and canned beans for a family of 5.'
        ]);

        Item::create([
            'name' => 'Hygiene Package',
            'category' => 'Hygiene',
            'unit' => 'kit',
            'description' => 'Includes soap, toothpaste, toothbrushes, laundry detergent, and towels.'
        ]);

        Item::create([
            'name' => 'Emergency Medical Kit',
            'category' => 'Medical',
            'unit' => 'kit',
            'description' => 'Essential surgical bandages, antiseptics, and basic first-aid tools.'
        ]);

        Item::create([
            'name' => 'Wheat Flour Sack (25kg)',
            'category' => 'Food',
            'unit' => 'bag',
            'description' => 'High-grade baking flour for local community kitchens.'
        ]);
    }
}