<?php

namespace Database\Seeders;

use App\Models\AidRequest;
use App\Models\AidRequestItem;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// class DatabaseSeeder extends Seeder
// {
//     public function run(): void
//     {
//         User::whereIn('email', [
//             'manager@reliefflow.com',
//             'coordinator@reliefflow.com',
//         ])->delete();

//         $manager = User::create([
//             'name' => 'Mahmoud Depot Manager',
//             'email' => 'manager@reliefflow.com',
//             'password' => Hash::make('password'),
//             'role' => 'depot_manager',
//             'status' => 'active',
//         ]);

//         $coordinator = User::create([
//             'name' => 'Ahmad Field Coordinator',
//             'email' => 'coordinator@reliefflow.com',
//             'password' => Hash::make('password'),
//             'role' => 'coordinator',
//             'status' => 'active',
//         ]);

//         Warehouse::query()->delete();

//         $central = Warehouse::create([
//             'name' => 'Central Gaza Depot',
//             'location' => 'Deir El-Balah, Salah Al-Din Road',
//             'latitude' => 31.4165,
//             'longitude' => 34.3510,
//             'capacity' => 10000,
//             'status' => 'active',
//         ]);

//         $south = Warehouse::create([
//             'name' => 'South Gaza Facility',
//             'location' => 'Rafah, Al-Salam Neighborhood',
//             'latitude' => 31.2980,
//             'longitude' => 34.2417,
//             'capacity' => 15000,
//             'status' => 'active',
//         ]);

//         Warehouse::create([
//             'name' => 'North Gaza Hub',
//             'location' => 'Jabalia Al-Balad',
//             'latitude' => 31.5280,
//             'longitude' => 34.4830,
//             'capacity' => 5000,
//             'status' => 'inactive',
//         ]);

//         Item::query()->delete();

//         $foodParcel = Item::create([
//             'name' => 'Family Food Parcel',
//             'category' => 'Food',
//             'unit' => 'box',
//             'description' => 'Contains sugar, rice, cooking oil, lentils, and canned goods.',
//         ]);

//         $hygieneKit = Item::create([
//             'name' => 'Hygiene Package',
//             'category' => 'Hygiene',
//             'unit' => 'kit',
//             'description' => 'Includes soap, toothpaste, toothbrushes, and towels.',
//         ]);

//         Item::create([
//             'name' => 'Emergency Medical Kit',
//             'category' => 'Medical',
//             'unit' => 'kit',
//             'description' => 'Essential surgical bandages and first-aid tools.',
//         ]);

//         Item::create([
//             'name' => 'Wheat Flour Sack (25kg)',
//             'category' => 'Food',
//             'unit' => 'bag',
//             'description' => 'High-grade baking flour for local community kitchens.',
//         ]);

//         Inventory::query()->delete();

//         Inventory::create(['warehouse_id' => $central->id, 'item_id' => $foodParcel->id, 'quantity' => 4200]);
//         Inventory::create(['warehouse_id' => $central->id, 'item_id' => $hygieneKit->id, 'quantity' => 850]);
//         Inventory::create(['warehouse_id' => $south->id, 'item_id' => $foodParcel->id, 'quantity' => 1600]);

//         AidRequest::query()->delete();

//         $sampleRequest = AidRequest::create([
//             'user_id' => $coordinator->id,
//             'location' => 'Khan Younis Distribution Point',
//             'latitude' => 31.3469,
//             'longitude' => 34.3029,
//             'notes' => 'Urgent — families with children waiting since morning.',
//             'status' => 'pending',
//             'priority' => 'critical',
//         ]);

//         AidRequestItem::create([
//             'aid_request_id' => $sampleRequest->id,
//             'item_id' => $foodParcel->id,
//             'quantity' => 150,
//         ]);
//     }
// }
