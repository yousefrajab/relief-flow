<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReliefFlow | Humanitarian Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
            color: #2d3748;
            overflow-x: hidden;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            color: #fff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand {
            padding: 15px 25px;
            font-size: 1.4rem;
            font-weight: 800;
            color: #3b82f6;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 25px;
        }
        .sidebar-menu {
            list-style: none;
            padding-left: 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 12px 25px;
            color: #9ca3af;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            color: #fff;
            background-color: rgba(59, 130, 246, 0.15);
            border-left: 4px solid #3b82f6;
            padding-left: 21px;
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .kpi-card {
            border: none;
            border-radius: 16px;
            background-color: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
        }
        .kpi-card.primary::before { background-color: #3b82f6; }
        .kpi-card.success::before { background-color: #10b981; }
        .kpi-card.warning::before { background-color: #f59e0b; }
        .kpi-card.danger::before { background-color: #ef4444; }
        
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            background-color: #fff;
        }
        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- الشريط الجانبي (Sidebar) -->
    <aside class="sidebar">
        <div class="sidebar-brand text-center text-lg-start">
            <i class="fas fa-box-open text-warning me-2"></i>ReliefFlow
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#"><i class="fas fa-chart-line me-3"></i>Dashboard</a></li>
            <li><a href="#warehouses"><i class="fas fa-warehouse me-3"></i>Warehouses</a></li>
            <li><a href="#inventory"><i class="fas fa-cubes me-3"></i>Inventory Stock</a></li>
            <li><a href="#requests"><i class="fas fa-file-signature me-3"></i>Field Requests</a></li>
            <li><a href="#shipments"><i class="fas fa-truck-loading me-3"></i>Shipments</a></li>
            <li><a href="#items"><i class="fas fa-boxes me-3"></i>Relief Items</a></li>
        </ul>
    </aside>

    <!-- مساحة المحتوى الرئيسية -->
    <main class="main-content">
        
        <!-- الهيدر ونظام تسجيل الخروج وتفاصيل المستخدم النشط -->
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-2 border-bottom pb-4">
            <div>
                <h1 class="fw-bold text-dark mb-1">Humanitarian Logistics Dashboard</h1>
                <p class="text-secondary mb-0">Logged in as: <strong class="text-primary">{{ auth()->user()->name }}</strong> 
                    <span class="badge bg-dark text-white text-uppercase ms-1 fs-8">{{ auth()->user()->role }}</span>
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold fs-7">
                    <i class="far fa-clock me-2 text-primary"></i>Live Tracker
                </span>
                
                <!-- نموذج تسجيل الخروج الآمن والكامل -->
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
                </form>
            </div>
        </div>

        <!-- صف بطاقات الإحصاءات والـ KPIs العلوية الذكية -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-card primary p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary fw-semibold d-block mb-1">Active Depots</span>
                            <span class="display-6 fw-bold text-dark">{{ $totalWarehouses }}</span>
                        </div>
                        <div class="bg-light p-3 rounded-circle text-primary">
                            <i class="fas fa-warehouse fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-card success p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary fw-semibold d-block mb-1">Relief Items</span>
                            <span class="display-6 fw-bold text-dark">{{ $totalItems }}</span>
                        </div>
                        <div class="bg-light p-3 rounded-circle text-success">
                            <i class="fas fa-boxes fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-card warning p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary fw-semibold d-block mb-1">Pending Requests</span>
                            <span class="display-6 fw-bold text-dark">{{ $pendingRequests }}</span>
                        </div>
                        <div class="bg-light p-3 rounded-circle text-warning">
                            <i class="fas fa-file-signature fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-card danger p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary fw-semibold d-block mb-1">In-Transit Shipments</span>
                            <span class="display-6 fw-bold text-dark">{{ $activeShipments }}</span>
                        </div>
                        <div class="bg-light p-3 rounded-circle text-danger">
                            <i class="fas fa-shipping-fast fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نظام الإنذار التلقائي للمخزون الحرج المنخفض (Low Stock Alerts) -->
        @if($lowStockAlerts->isNotEmpty())
        <div class="card card-custom p-4 mb-5 border-0 bg-white shadow-sm">
            <h4 class="fw-bold text-warning mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Critical Low Stock Alerts</h4>
            <div class="row g-3">
                @foreach($lowStockAlerts as $alert)
                <div class="col-md-6">
                    <div class="alert alert-warning border-0 shadow-sm mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <i class="fas fa-warehouse text-warning me-2"></i>
                            <strong class="text-dark">{{ $alert->warehouse->name }}</strong>: 
                            <span class="text-secondary">{{ $alert->item->name }}</span> is running critically low!
                        </div>
                        <span class="badge bg-danger fs-7 fw-bold px-3 py-2 rounded-pill">Only {{ number_format($alert->quantity) }} {{ $alert->item->unit }}s left</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 1. قسم المخازن المتاحة -->
        <section id="warehouses" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-warehouse me-2 text-primary"></i>Active Warehouses & Depots</h3>
                @if(auth()->user()->role == 'admin')
                    <!-- لا يعرض الزر إلا للآدمن فقط -->
                    <button class="btn btn-primary px-4 py-2 fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                        <i class="fas fa-plus me-2"></i>Add Warehouse
                    </button>
                @endif
            </div>

            <div class="row g-4">
                @foreach($warehouses as $warehouse)
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">{{ $warehouse->name }}</h5>
                            @if($warehouse->status == 'active')
                                <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-secondary badge-status"><i class="fas fa-pause-circle me-1"></i>Inactive</span>
                            @endif
                        </div>
                        <p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $warehouse->location }}</p>
                        <p class="text-secondary mb-3">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Capacity: <strong>{{ number_format($warehouse->capacity) }}</strong> units
                        </p>
                        
                        <!-- أزرار التعديل والحذف للآدمن فقط مدمجة باحترافية تامة -->
                        @if(auth()->user()->role == 'admin')
                            <div class="d-flex gap-2 border-top pt-3">
                                <!-- زر التعديل المطور المرتبط بمسح وحقن الـ JavaScript -->
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="openEditWarehouseModal({{ $warehouse->id }}, '{{ $warehouse->name }}', '{{ $warehouse->location }}', {{ $warehouse->capacity }})">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                <!-- نموذج الحذف الآمن والكامل -->
                                <form action="{{ route('warehouse.delete', $warehouse->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this warehouse?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                        <i class="fas fa-trash-alt me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- 2. قسم المخزون الحي في المستودعات (يعرض للآدمن وأمين المستودع فقط) -->
        @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
        <section id="inventory" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-cubes me-2 text-primary"></i>Live Inventory Stock</h3>
                <button class="btn btn-warning text-dark px-4 py-2 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <i class="fas fa-shipping-fast me-2"></i>Add/Adjust Stock
                </button>
            </div>

            <div class="card card-custom p-4 bg-white border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Warehouse</th>
                                <th scope="col">Relief Item</th>
                                <th scope="col">Category</th>
                                <th scope="col" class="text-end">Current Quantity</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inventory)
                            <tr>
                                <td class="fw-bold text-dark">{{ $inventory->warehouse->name }}</td>
                                <td class="fw-semibold text-primary">{{ $inventory->item->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                                        {{ $inventory->item->category }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success fs-5">{{ number_format($inventory->quantity) }}</td>
                                <td><span class="badge bg-info text-white px-2 py-1">{{ $inventory->item->unit }}</span></td>
                                <td class="text-muted">{{ $inventory->updated_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>No inventory stock added yet. Click "Add/Adjust Stock" to dispatch some goods.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif

        <!-- 3. قسم طلبات الاحتياج الميداني والموافقة والترحيل اللوجستي -->
        <section id="requests" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-file-signature me-2 text-primary"></i>Active Field Requests</h3>
                @if(in_array(auth()->user()->role, ['admin', 'coordinator']))
                    <!-- المنسق أو الآدمن فقط من يستطيع تقديم طلب جديد -->
                    <button class="btn btn-danger px-4 py-2 fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#addAidRequestModal">
                        <i class="fas fa-notes-medical me-2"></i>Submit Field Request
                    </button>
                @endif
            </div>

            <div class="card card-custom p-4 bg-white border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Location</th>
                                <th scope="col">Requested Items</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Coordinator</th>
                                <th scope="col">Status</th>
                                @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
                                    <!-- لا يرى أفعال الترحيل إلا الآدمن أو أمين المستودع -->
                                    <th scope="col" class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aidRequests as $request)
                            <tr>
                                <td class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $request->location }}</td>
                                <td>
                                    @foreach($request->requestItems as $reqItem)
                                        <span class="fw-semibold text-primary d-block">{{ $reqItem->item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($request->requestItems as $reqItem)
                                        <span class="fw-bold text-dark">{{ number_format($reqItem->quantity) }} ({{ $reqItem->item->unit }})</span>
                                    @endforeach
                                </td>
                                <td><span class="text-secondary fw-semibold">{{ $request->user->name }}</span></td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark badge-status"><i class="fas fa-clock me-1"></i>Pending</span>
                                    @elseif($request->status == 'dispatched')
                                        <span class="badge bg-info text-white badge-status"><i class="fas fa-truck me-1"></i>Dispatched</span>
                                    @else
                                        <span class="badge bg-success text-white badge-status"><i class="fas fa-check-circle me-1"></i>Delivered</span>
                                    @endif
                                </td>
                                @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
                                    <td class="text-center">
                                        @if($request->status == 'pending')
                                            <button class="btn btn-sm btn-outline-success px-3 rounded-pill fw-bold" onclick="openDispatchModal({{ $request->id }}, '{{ $request->location }}')">
                                                <i class="fas fa-shipping-fast me-1"></i>Approve & Ship
                                            </button>
                                        @else
                                            <span class="text-success fs-7 fw-bold"><i class="fas fa-clipboard-check me-1"></i>Processed</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>No active field requests. Click "Submit Field Request" to create a new demand.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 4. قسم الشحنات واللوجستيات الصادرة -->
        <section id="shipments" class="mb-5">
            <h3 class="fw-bold mb-4 text-dark"><i class="fas fa-truck-loading me-2 text-primary"></i>Active Outgoing Shipments</h3>
            <div class="card card-custom p-4 bg-white border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Destination</th>
                                <th scope="col">From Warehouse</th>
                                <th scope="col">Driver Details</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Live QR Tracking Code</th>
                                <th scope="col" class="text-center">Actual Delivery Time</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $shipment)
                            <tr>
                                <td class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $shipment->aidRequest->location }}</td>
                                <td class="fw-semibold text-secondary">{{ $shipment->warehouse->name }}</td>
                                <td>
                                    <span class="d-block fw-bold text-dark"><i class="fas fa-user-circle me-2 text-muted"></i>{{ $shipment->driver_name }}</span>
                                    <span class="text-muted fs-7"><i class="fas fa-phone me-2"></i>{{ $shipment->driver_phone }}</span>
                                </td>
                                <td>
                                    @if($shipment->status == 'dispatched')
                                        <span class="badge bg-info text-white badge-status"><i class="fas fa-road me-1"></i>In Transit</span>
                                    @else
                                        <span class="badge bg-success text-white badge-status"><i class="fas fa-check-double me-1"></i>Delivered</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-block text-center p-2 bg-light rounded border">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $shipment->qr_code_token }}" alt="QR Code" class="img-fluid shadow-sm rounded" style="width: 65px; height: 60px;">
                                        <span class="d-block font-monospace fs-7 fw-bold mt-1 text-dark">{{ $shipment->qr_code_token }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($shipment->delivered_at)
                                        @php
                                            $deliveredDate = \Carbon\Carbon::parse($shipment->delivered_at);
                                        @endphp
                                        <span class="text-success fw-bold fs-7 d-block">
                                            <i class="fas fa-check-circle me-1"></i>{{ $deliveredDate->format('Y-m-d H:i:s') }}
                                        </span>
                                        <small class="text-muted">({{ $deliveredDate->diffForHumans() }})</small>
                                    @else
                                        <span class="text-muted fs-7"><i class="fas fa-spinner fa-spin me-1"></i>Waiting for driver...</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <!-- إمكانية تأكيد الاستلام تظهر للآدمن والمنسق فقط في الميدان -->
                                        @if($shipment->status == 'dispatched')
                                            @if(in_array(auth()->user()->role, ['admin', 'coordinator']))
                                                <form action="{{ route('shipment.deliver') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="shipment_id" value="{{ $shipment->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold px-3 shadow-sm">
                                                        <i class="fas fa-check-double me-1"></i>Confirm Delivery
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted fs-7 fw-semibold"><i class="fas fa-lock me-1"></i>Pending field...</span>
                                            @endif
                                        @else
                                            <span class="text-success fw-bold me-2"><i class="fas fa-clipboard-check me-1"></i>Delivered</span>
                                        @endif
                                        
                                        <!-- يتاح زر طباعة المنافيست للآدمن وأمين المستودع فقط -->
                                        @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
                                            <a href="{{ route('shipment.print', $shipment->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill fw-bold px-3 shadow-sm">
                                                <i class="fas fa-print me-1"></i>Print Manifest
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>No outgoing shipments dispatched yet. Approve any pending field request to generate a shipment.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 5. قسم المواد الإغاثية المتوفرة في النظام -->
        <section id="items">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes me-2 text-primary"></i>Available Relief Items</h3>
                @if(auth()->user()->role == 'admin')
                    <button class="btn btn-success px-4 py-2 fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus me-2"></i>Add Relief Item
                    </button>
                @endif
            </div>

            <div class="card card-custom p-4 bg-white border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Standard Unit</th>
                                <th scope="col">Description</th>
                                @if(auth()->user()->role == 'admin')
                                    <th scope="col" class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <th scope="row" class="fw-bold">{{ $item->id }}</th>
                                <td class="fw-semibold text-primary">{{ $item->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td><span class="badge bg-info text-white px-2 py-1">{{ $item->unit }}</span></td>
                                <td class="text-muted" style="max-width: 300px;">{{ $item->description }}</td>
                                @if(auth()->user()->role == 'admin')
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" onclick="openEditItemModal({{ $item->id }}, '{{ $item->name }}', '{{ $item->category }}', '{{ $item->unit }}', '{{ $item->description }}')">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <form action="{{ route('item.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this relief item?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- 1. النافذة المنبثقة لإضافة مستودع جديد (Add Warehouse Modal) -->
    <div class="modal fade" id="addWarehouseModal" tabindex="-1" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="addWarehouseModalLabel"><i class="fas fa-warehouse me-2 text-primary"></i>Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('warehouse.store') }}" method="POST">
                    @csrf 
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Warehouse Name</label>
                            <input type="text" class="form-control py-2 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Gaza Port Depot">
                            @error('name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Location</label>
                            <input type="text" class="form-control py-2 @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" required placeholder="e.g. Gaza City, Port Area">
                            @error('location')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="capacity" class="form-label fw-semibold">Max Capacity (Units)</label>
                            <input type="number" class="form-control py-2 @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity') }}" placeholder="e.g. 25000">
                            @error('capacity')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fas fa-save me-2"></i>Save Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 1.1 النافذة المنبثقة لتعديل مستودع موجود (Edit Warehouse Modal) -->
    <div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom">
                    <h5 class="modal-title fw-bold" id="editWarehouseModalLabel"><i class="fas fa-warehouse me-2"></i>Edit Warehouse</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- نربطها برابط الحفظ ونمرر الحقول للتحقق السليم -->
                <form action="{{ route('warehouse.store') }}" method="POST">
                    @csrf 
                    <input type="hidden" id="edit_warehouse_id" name="warehouse_id" value="{{ old('warehouse_id') }}">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label fw-semibold">Warehouse Name</label>
                            <input type="text" class="form-control py-2 @error('name') is-invalid @enderror" id="edit_name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_location" class="form-label fw-semibold">Location</label>
                            <input type="text" class="form-control py-2 @error('location') is-invalid @enderror" id="edit_location" name="location" value="{{ old('location') }}" required>
                            @error('location')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_capacity" class="form-label fw-semibold">Max Capacity (Units)</label>
                            <input type="number" class="form-control py-2 @error('capacity') is-invalid @enderror" id="edit_capacity" name="capacity" value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fas fa-save me-2"></i>Update Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. النافذة المنبثقة لإضافة مادة إغاثية جديدة (Add Item Modal) -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="addItemModalLabel"><i class="fas fa-boxes me-2 text-success"></i>Add New Relief Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('item.store') }}" method="POST">
                    @csrf 
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="item_name" class="form-label fw-semibold">Item Name</label>
                            <input type="text" class="form-control py-2 @error('item_name') is-invalid @enderror" id="item_name" name="item_name" value="{{ old('item_name') }}" required placeholder="e.g. Canned Tuna Box">
                            @error('item_name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">Category</label>
                            <select class="form-select py-2 @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="" disabled selected>Choose category...</option>
                                <option value="Food" {{ old('category') == 'Food' ? 'selected' : '' }}>Food (غذاء)</option>
                                <option value="Hygiene" {{ old('category') == 'Hygiene' ? 'selected' : '' }}>Hygiene (نظافة)</option>
                                <option value="Medical" {{ old('category') == 'Medical' ? 'selected' : '' }}>Medical (مستلزمات طبية)</option>
                                <option value="Shelter" {{ old('category') == 'Shelter' ? 'selected' : '' }}>Shelter (إيواء وأغطية)</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label fw-semibold">Standard Unit</label>
                            <input type="text" class="form-control py-2 @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" required placeholder="e.g. box, kit, bag, kg">
                            @error('unit')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional description of the item contents...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="fas fa-save me-2"></i>Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2.1 النافذة المنبثقة لتعديل مادة إغاثية موجودة (Edit Item Modal) -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-bottom">
                    <h5 class="modal-title fw-bold" id="editItemModalLabel"><i class="fas fa-boxes me-2"></i>Edit Relief Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('item.update') }}" method="POST">
                    @csrf 
                    <input type="hidden" id="edit_item_id" name="item_id" value="{{ old('item_id') }}">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_item_name" class="form-label fw-semibold">Item Name</label>
                            <input type="text" class="form-control py-2 @error('item_name') is-invalid @enderror" id="edit_item_name" name="item_name" value="{{ old('item_name') }}" required>
                            @error('item_name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="form-label fw-semibold">Category</label>
                            <select class="form-select py-2 @error('category') is-invalid @enderror" id="edit_category" name="category" required>
                                <option value="Food" {{ old('category') == 'Food' ? 'selected' : '' }}>Food (غذاء)</option>
                                <option value="Hygiene" {{ old('category') == 'Hygiene' ? 'selected' : '' }}>Hygiene (نظافة)</option>
                                <option value="Medical" {{ old('category') == 'Medical' ? 'selected' : '' }}>Medical (مستلزمات طبية)</option>
                                <option value="Shelter" {{ old('category') == 'Shelter' ? 'selected' : '' }}>Shelter (إيواء وأغطية)</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit" class="form-label fw-semibold">Standard Unit</label>
                            <input type="text" class="form-control py-2 @error('unit') is-invalid @enderror" id="edit_unit" name="unit" value="{{ old('unit') }}" required>
                            @error('unit')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="fas fa-save me-2"></i>Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. النافذة المنبثقة لتعبئة وتعديل المخزون (Add/Adjust Inventory Modal) -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="addInventoryModalLabel"><i class="fas fa-shipping-fast me-2 text-warning"></i>Dispatch / Adjust Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf 
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label fw-semibold">Select Warehouse</label>
                            <select class="form-select py-2 @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                <option value="" disabled selected>Choose warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->location }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="item_id" class="form-label fw-semibold">Select Relief Item</label>
                            <select class="form-select py-2 @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                <option value="" disabled selected>Choose item...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-semibold">Quantity to Add</label>
                            <input type="number" class="form-control py-2 @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required placeholder="e.g. 5000">
                            @error('quantity')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-bold"><i class="fas fa-check me-2"></i>Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. النافذة المنبثقة لإنشاء طلب احتياج ميداني جديد (Add Aid Request Modal) -->
    <div class="modal fade" id="addAidRequestModal" tabindex="-1" aria-labelledby="addAidRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="addAidRequestModalLabel"><i class="fas fa-file-signature me-2 text-danger"></i>Submit Field Aid Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('aid_request.store') }}" method="POST">
                    @csrf 
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="location_req" class="form-label fw-semibold">Target Distribution Location</label>
                            <input type="text" class="form-control py-2 @error('location') is-invalid @enderror" id="location_req" name="location" value="{{ old('location') }}" required placeholder="e.g. Al-Mawasi Refugee Camp">
                            @error('location')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_item_id" class="form-label fw-semibold">Select Required Item</label>
                            <select class="form-select py-2 @error('request_item_id') is-invalid @enderror" id="request_item_id" name="request_item_id" required>
                                <option value="" disabled selected>Choose item...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('request_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('request_item_id')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_quantity" class="form-label fw-semibold">Required Quantity</label>
                            <input type="number" class="form-control py-2 @error('request_quantity') is-invalid @enderror" id="request_quantity" name="request_quantity" value="{{ old('request_quantity') }}" required placeholder="e.g. 1500">
                            @error('request_quantity')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Field Notes / Special Instructions</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional notes about the current humanitarian situation...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="fas fa-paper-plane me-2"></i>Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. النافذة المنبثقة التفاعلية الذكية للموافقة على الطلب وتعيين السائق والترحيل (Dispatch Shipment Modal) -->
    <div class="modal fade" id="dispatchShipmentModal" tabindex="-1" aria-labelledby="dispatchShipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-bottom">
                    <h5 class="modal-title fw-bold" id="dispatchShipmentModalLabel"><i class="fas fa-truck me-2"></i>Approve & Dispatch Shipment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('shipment.dispatch') }}" method="POST">
                    @csrf 
                    <input type="hidden" id="dispatch_aid_request_id" name="aid_request_id" value="{{ old('aid_request_id') }}">
                    
                    <div class="modal-body p-4">
                        <p class="text-secondary fw-semibold">
                            Dispatching aid to target location: <strong class="text-danger" id="dispatch_location_placeholder">[Location]</strong>
                        </p>
                        <hr>
                        
                        <div class="mb-3">
                            <label for="dispatch_warehouse_id" class="form-label fw-semibold text-dark">Select Dispatch Warehouse (Stock Source)</label>
                            <select class="form-select py-2 @error('dispatch_warehouse_id') is-invalid @enderror" id="dispatch_warehouse_id" name="dispatch_warehouse_id" required>
                                <option value="" disabled selected>Choose warehouse with sufficient stock...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('dispatch_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} ({{ $warehouse->location }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dispatch_warehouse_id')
                                <div class="invalid-feedback fw-semibold d-block mt-2 text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="driver_name" class="form-label fw-semibold text-dark">Driver Name</label>
                            <input type="text" class="form-control py-2 @error('driver_name') is-invalid @enderror" id="driver_name" name="driver_name" value="{{ old('driver_name') }}" required placeholder="e.g. Mahmoud Yasin">
                            @error('driver_name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="driver_phone" class="form-label fw-semibold text-dark">Driver Phone Number</label>
                            <input type="text" class="form-control py-2 @error('driver_phone') is-invalid @enderror" id="driver_phone" name="driver_phone" value="{{ old('driver_phone') }}" required placeholder="e.g. +972 599123456">
                            @error('driver_phone')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-truck-moving me-2"></i>Confirm Dispatch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- تفعيل SweetAlert2 لإشعارات النجاح -->
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Operation Successful!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Awesome',
                customClass: {
                    confirmButton: 'btn btn-primary px-4 py-2 fw-semibold rounded-pill'
                },
                buttonsStyling: false
            });
        </script>
    @endif

    <!-- كود جافا سكريبت تفاعلي لنقل معلومات الطلب المحددة إلى نافذة الترحيل تلقائياً -->
    <script>
        function openDispatchModal(requestId, location) {
            document.getElementById('dispatch_aid_request_id').value = requestId;
            document.getElementById('dispatch_location_placeholder').innerText = location;
            var myModal = new bootstrap.Modal(document.getElementById('dispatchShipmentModal'));
            myModal.show();
        }
        
        // كود حقن تفاصيل التعديل للمستودع في المودال تلقائياً
        function openEditWarehouseModal(id, name, location, capacity) {
            document.getElementById('edit_warehouse_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_capacity').value = capacity;
            var myModal = new bootstrap.Modal(document.getElementById('editWarehouseModal'));
            myModal.show();
        }

        // كود حقن تفاصيل المادة الإغاثية في مودال التعديل تلقائياً
        function openEditItemModal(id, name, category, unit, description) {
            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_item_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_unit').value = unit;
            document.getElementById('edit_description').value = description;
            var myModal = new bootstrap.Modal(document.getElementById('editItemModal'));
            myModal.show();
        }
    </script>

    <!-- سكربت ذكي يتعرف على مكان الأخطاء بدقة متناهية ويبقي النافذة المنبثقة المناسبة مفتوحة تلقائياً -->
    @if($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                @if($errors->hasAny(['name', 'location', 'capacity']))
                    // التحقق ما إذا كانت الأخطاء لعملية إضافة جديدة أم تعديل
                    var editId = "{{ old('warehouse_id') }}";
                    if (editId) {
                        document.getElementById('edit_warehouse_id').value = editId;
                        var myModal = new bootstrap.Modal(document.getElementById('editWarehouseModal'));
                        myModal.show();
                    } else {
                        var warehouseModal = new bootstrap.Modal(document.getElementById('addWarehouseModal'));
                        warehouseModal.show();
                    }
                @elseif($errors->hasAny(['item_name', 'category', 'unit', 'description']))
                    var editItemId = "{{ old('item_id') }}";
                    if (editItemId) {
                        document.getElementById('edit_item_id').value = editItemId;
                        var myModal = new bootstrap.Modal(document.getElementById('editItemModal'));
                        myModal.show();
                    } else {
                        var itemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
                        itemModal.show();
                    }
                @elseif($errors->hasAny(['warehouse_id', 'item_id', 'quantity']))
                    var inventoryModal = new bootstrap.Modal(document.getElementById('addInventoryModal'));
                    inventoryModal.show();
                @elseif($errors->hasAny(['location', 'request_item_id', 'request_quantity', 'notes']))
                    var aidRequestModal = new bootstrap.Modal(document.getElementById('addAidRequestModal'));
                    aidRequestModal.show();
                @elseif($errors->hasAny(['aid_request_id', 'dispatch_warehouse_id', 'driver_name', 'driver_phone']))
                    var reqId = "{{ old('aid_request_id') }}";
                    if (reqId) {
                        document.getElementById('dispatch_aid_request_id').value = reqId;
                    }
                    var dispatchModal = new bootstrap.Modal(document.getElementById('dispatchShipmentModal'));
                    dispatchModal.show();
                @endif
            });
        </script>
    @endif
</body>
</html>