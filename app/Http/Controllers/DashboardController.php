<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // جلب جميع المخازن والمواد من قاعدة البيانات
        $warehouses = Warehouse::all();
        $items = Item::all();

        return view('dashboard', compact('warehouses', 'items'));
    }

    // دالة استقبال وحفظ المستودع الجديد
    public function storeWarehouse(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        // 2. إنشاء وحفظ المستودع في قاعدة البيانات
        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->location = $request->location;
        $warehouse->capacity = $request->capacity;
        $warehouse->status = 'active'; // افتراضياً نشط
        $warehouse->save();

        // 3. إعادة توجيه المستخدم لصفحة لوحة التحكم مع رسالة نجاح
        return redirect('/')->with('success', 'New Warehouse has been added successfully!');
    }
}