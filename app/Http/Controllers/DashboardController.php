<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // جلب جميع المخازن والمواد الإغاثية من قاعدة البيانات
        $warehouses = Warehouse::all();
        $items = Item::all();

        // تمرير البيانات لصفحة الـ Blade (واجهة الموقع)
        return view('dashboard', compact('warehouses', 'items'));
    }
}