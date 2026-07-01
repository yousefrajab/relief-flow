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
        
        <!-- القسم الأول: المخازن المتاحة -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4 text-dark"><i class="fas fa-warehouse me-2 text-primary"></i>Active Warehouses & Depots</h3>
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

    <!-- Footer -->
    <footer class="py-4 text-center text-muted bg-white border-top mt-5">
        <p class="mb-0">&copy; 2026 ReliefFlow Logistics System. Powered by Yousef Al Khateeb.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>