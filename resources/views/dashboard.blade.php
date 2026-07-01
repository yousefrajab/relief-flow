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
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
            color: #2d3748;
        }
        .hero-banner {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            color: #fff;
            padding: 60px 0;
            border-bottom: 4px solid #0d6efd;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }
        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- Header Banner -->
    <header class="hero-banner mb-5">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-2"><i class="fas fa-box-open me-3 text-warning"></i>ReliefFlow Operations</h1>
            <p class="lead mb-0">Real-time humanitarian logistics and supply chain distribution tracker.</p>
        </div>
    </header>

    <div class="container pb-5">

        <!-- عرض رسائل النجاح الخضراء بعد الحفظ بنجاح -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
                    <div>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- القسم الأول: المخازن المتاحة -->
        <div class="mb-5">
            <!-- الهيدر مضاف له زر الإضافة التفاعلي الجديد -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-warehouse me-2 text-primary"></i>Active Warehouses & Depots</h3>
                <button class="btn btn-primary px-4 py-2 fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                    <i class="fas fa-plus me-2"></i>Add Warehouse
                </button>
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
                        <p class="text-secondary mb-0">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Capacity: <strong>{{ number_format($warehouse->capacity) }}</strong> units
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- القسم الثاني: المواد الإغاثية المتوفرة -->
        <div>
            <h3 class="fw-bold mb-4 text-dark"><i class="fas fa-boxes me-2 text-primary"></i>Available Relief Items</h3>
            <div class="card card-custom p-4 bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Standard Unit</th>
                                <th scope="col">Description</th>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- النافذة المنبثقة لإضافة مستودع جديد (Add Warehouse Modal) -->
    <div class="modal fade" id="addWarehouseModal" tabindex="-1" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="addWarehouseModalLabel"><i class="fas fa-warehouse me-2 text-primary"></i>Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- نموذج إرسال البيانات المربوط بمسار الحفظ الفعلي لـ Laravel -->
                <form action="{{ route('warehouse.store') }}" method="POST">
                    @csrf <!-- كود الأمان والحماية الإلزامي في لارافيل -->
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Warehouse Name</label>
                            <input type="text" class="form-control py-2" id="name" name="name" required placeholder="e.g. Gaza Port Depot">
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Location</label>
                            <input type="text" class="form-control py-2" id="location" name="location" required placeholder="e.g. Gaza City, Port Area">
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label fw-semibold">Max Capacity (Units)</label>
                            <input type="number" class="form-control py-2" id="capacity" name="capacity" placeholder="e.g. 25000">
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

    <!-- Footer -->
    <footer class="py-4 text-center text-muted bg-white border-top mt-5">
        <p class="mb-0">&copy; 2026 ReliefFlow Logistics System. Powered by Yousef Al Khateeb.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>