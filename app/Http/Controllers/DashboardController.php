<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\AidRequest;
use App\Models\AidRequestItem;
use App\Models\Shipment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم بجميع البيانات والمخزون الحي والعمليات والـ KPIs والتنبيهات
     */
    public function index()
    {
        // جلب المخازن، المواد، والمخزون الحي مرتبة بالأحدث
        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $items = Item::orderBy('id', 'desc')->get();
        $inventories = Inventory::with(['warehouse', 'item'])->orderBy('id', 'desc')->get();

        // جلب طلبات المساعدات الميدانية مع تفاصيلها والمواد المطلوبة بداخلها
        $aidRequests = AidRequest::with(['requestItems.item', 'user'])->orderBy('id', 'desc')->get();

        // جلب الشحنات الصادرة التي خرجت من المخازن
        $shipments = Shipment::with(['aidRequest', 'warehouse'])->orderBy('id', 'desc')->get();

        // حساب الإحصاءات والـ KPIs بشكل ديناميكي دقيق للبطاقات العلوية
        $totalWarehouses = Warehouse::count();
        $totalItems = Item::count();
        $pendingRequests = AidRequest::where('status', 'pending')->count();
        $activeShipments = Shipment::where('status', 'dispatched')->count();

        // جلب قائمة المواد التي يقل مخزونها في أي مستودع عن 1,000 وحدة للتنبيه الفوري
        $lowStockAlerts = Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->get();

        return view('dashboard', compact(
            'warehouses', 
            'items', 
            'inventories', 
            'aidRequests', 
            'shipments',
            'totalWarehouses',
            'totalItems',
            'pendingRequests',
            'activeShipments',
            'lowStockAlerts'
        ));
    }

    /**
     * التحقق وحفظ مستودع جديد (صلاحية الآدمن فقط)
     */
    public function storeWarehouse(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'location' => 'required|string|min:5|max:255',
            'capacity' => 'required|integer|min:100',
        ], [
            'name.required' => 'Warehouse name is required.',
            'name.min' => 'The warehouse name must be at least 3 characters.',
            'location.required' => 'Location is required.',
            'location.min' => 'The location description must be at least 5 characters.',
            'capacity.required' => 'Max capacity is required.',
            'capacity.min' => 'A real logistics warehouse capacity must be at least 100 units.',
        ]);

        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->location = $request->location;
        $warehouse->capacity = $request->capacity;
        $warehouse->status = 'active';
        $warehouse->save();

        return redirect('/')->with('success', 'New Warehouse has been added successfully!');
    }

    /**
     * تعديل وحفظ بيانات مستودع موجود (صلاحية الآدمن فقط)
     */
    public function updateWarehouse(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|min:3|max:255',
            'location' => 'required|string|min:5|max:255',
            'capacity' => 'required|integer|min:100',
        ], [
            'name.required' => 'Warehouse name is required.',
            'name.min' => 'The warehouse name must be at least 3 characters.',
            'location.required' => 'Location is required.',
            'location.min' => 'The location description must be at least 5 characters.',
            'capacity.required' => 'Max capacity is required.',
            'capacity.min' => 'A real logistics warehouse capacity must be at least 100 units.',
        ]);

        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        $warehouse->name = $request->name;
        $warehouse->location = $request->location;
        $warehouse->capacity = $request->capacity;
        $warehouse->save();

        return redirect('/')->with('success', 'Warehouse has been updated successfully!');
    }

    /**
     * حذف مستودع بالكامل (صلاحية الآدمن فقط)
     */
    public function deleteWarehouse($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        return redirect('/')->with('success', 'Warehouse has been deleted successfully!');
    }

    /**
     * التحقق وحفظ مادة إغاثية جديدة (صلاحية الآدمن فقط)
     */
    public function storeItem(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'item_name' => 'required|string|min:3|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'item_name.required' => 'The relief item name is required.',
            'item_name.min' => 'The relief item name must be at least 3 characters.',
            'category.required' => 'Please select a valid category.',
            'unit.required' => 'Standard measurement unit is required.',
        ]);

        $item = new Item();
        $item->name = $request->item_name;
        $item->category = $request->category;
        $item->unit = $request->unit;
        $item->description = $request->description;
        $item->save();

        return redirect('/')->with('success', 'New Relief Item has been added successfully!');
    }

    /**
     * تعديل بيانات مادة إغاثية موجودة (صلاحية الآدمن فقط)
     */
    public function updateItem(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'item_name' => 'required|string|min:3|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'item_name.required' => 'The relief item name is required.',
            'item_name.min' => 'The relief item name must be at least 3 characters.',
            'category.required' => 'Please select a valid category.',
            'unit.required' => 'Standard measurement unit is required.',
        ]);

        $item = Item::findOrFail($request->item_id);
        $item->name = $request->item_name;
        $item->category = $request->category;
        $item->unit = $request->unit;
        $item->description = $request->description;
        $item->save();

        return redirect('/')->with('success', 'Relief Item has been updated successfully!');
    }

    /**
     * حذف مادة إغاثية بالكامل (صلاحية الآدمن فقط)
     */
    public function deleteItem($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $item = Item::findOrFail($id);
        $item->delete();

        return redirect('/')->with('success', 'Relief Item has been deleted successfully!');
    }

    /**
     * إدارة وتحديث المخزون (صلاحية الآدمن وأمين المستودع)
     */
    public function inventoryStore(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'depot_manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'warehouse_id.required' => 'Please select a warehouse.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',
            'item_id.required' => 'Please select a relief item.',
            'item_id.exists' => 'The selected item is invalid.',
            'quantity.required' => 'Please enter a quantity.',
            'quantity.min' => 'The quantity must be at least 1 unit.',
        ]);

        $inventory = Inventory::where('warehouse_id', $request->warehouse_id)
                              ->where('item_id', $request->item_id)
                              ->first();

        if ($inventory) {
            $inventory->quantity += $request->quantity;
            $inventory->save();
        } else {
            $inventory = new Inventory();
            $inventory->warehouse_id = $request->warehouse_id;
            $inventory->item_id = $request->item_id;
            $inventory->quantity = $request->quantity;
            $inventory->save();
        }

        return redirect('/')->with('success', 'Inventory stock has been successfully updated!');
    }

    /**
     * استقبال وحفظ طلب مساعدات ميداني جديد (صلاحية الآدمن والمنسق)
     */
    public function storeAidRequest(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'coordinator'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'location' => 'required|string|min:5|max:255',
            'request_item_id' => 'required|exists:items,id',
            'request_quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ], [
            'location.required' => 'Please specify the target distribution location.',
            'location.min' => 'The location description must be at least 5 characters.',
            'request_item_id.required' => 'Please select the required relief item.',
            'request_quantity.required' => 'Please specify the required quantity.',
            'request_quantity.min' => 'The required quantity must be at least 1 unit.',
        ]);

        $user = auth()->user();

        $aidRequest = new AidRequest();
        $aidRequest->user_id = $user->id;
        $aidRequest->location = $request->location;
        $aidRequest->notes = $request->notes;
        $aidRequest->status = 'pending';
        $aidRequest->save();

        $requestItem = new AidRequestItem();
        $requestItem->aid_request_id = $aidRequest->id;
        $requestItem->item_id = $request->request_item_id;
        $requestItem->quantity = $request->request_quantity;
        $requestItem->save();

        return redirect('/')->with('success', 'Field Aid Request has been submitted and is pending approval!');
    }

    /**
     * الموافقة والترحيل اللوجستي الذكي للشحنات (صلاحية الآدمن وأمين المستودع)
     */
    public function dispatchShipment(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'depot_manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'aid_request_id' => 'required|exists:aid_requests,id',
            'dispatch_warehouse_id' => 'required|exists:warehouses,id',
            'driver_name' => 'required|string|min:3|max:255',
            'driver_phone' => 'required|string|min:7|max:20',
        ], [
            'dispatch_warehouse_id.required' => 'Please select the dispatch warehouse to pull stock from.',
            'driver_name.required' => 'Driver name is required.',
            'driver_phone.required' => 'Driver phone number is required.',
        ]);

        $aidRequest = AidRequest::with('requestItems.item')->findOrFail($request->aid_request_id);
        $warehouse = Warehouse::findOrFail($request->dispatch_warehouse_id);

        foreach ($aidRequest->requestItems as $reqItem) {
            $inventory = Inventory::where('warehouse_id', $warehouse->id)
                                  ->where('item_id', $reqItem->item_id)
                                  ->first();

            if (!$inventory || $inventory->quantity < $reqItem->quantity) {
                $itemName = $reqItem->item->name;
                $available = $inventory ? $inventory->quantity : 0;
                
                return redirect('/')->withErrors([
                    'dispatch_warehouse_id' => "Insufficient stock in '{$warehouse->name}' for item '{$itemName}' (Requested: " . number_format($reqItem->quantity) . " units, Available: " . number_format($available) . " units)."
                ])->withInput();
            }
        }

        foreach ($aidRequest->requestItems as $reqItem) {
            $inventory = Inventory::where('warehouse_id', $warehouse->id)
                                  ->where('item_id', $reqItem->item_id)
                                  ->first();
            $inventory->quantity -= $reqItem->quantity;
            $inventory->save();
        }

        $aidRequest->status = 'dispatched';
        $aidRequest->save();

        $shipment = new Shipment();
        $shipment->aid_request_id = $aidRequest->id;
        $shipment->warehouse_id = $warehouse->id;
        $shipment->driver_name = $request->driver_name;
        $shipment->driver_phone = $request->driver_phone;
        $shipment->status = 'dispatched';
        $shipment->qr_code_token = 'RF-' . strtoupper(bin2hex(random_bytes(4)));
        $shipment->save();

        return redirect('/')->with('success', "Shipment dispatched successfully! Tracking QR Token: {$shipment->qr_code_token}");
    }

    /**
     * تأكيد الاستلام الميداني الفعلي للشحنة (صلاحية الآدمن والمنسق)
     */
    public function deliverShipment(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'coordinator'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
        ]);

        $shipment = Shipment::findOrFail($request->shipment_id);
        $shipment->status = 'delivered';
        $shipment->delivered_at = now();
        $shipment->save();

        $aidRequest = AidRequest::findOrFail($shipment->aid_request_id);
        $aidRequest->status = 'delivered';
        $aidRequest->save();

        return redirect('/')->with('success', 'Shipment has been successfully received and verified in the field!');
    }

    /**
     * جلب بيانات وتوليد واجهة طباعة وثيقة الشحن والمنافيست الرسمية
     */
    public function printShipment($id)
    {
        $shipment = Shipment::with(['aidRequest.requestItems.item', 'warehouse'])->findOrFail($id);
        return view('print-shipment', compact('shipment'));
    }
}