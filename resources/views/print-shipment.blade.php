<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatched Manifest | {{ $shipment->qr_code_token }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* طابع السجلات اللوجستية الرسمية */
            color: #000;
            background-color: #fff;
            padding: 30px;
        }
        .manifest-container {
            border: 2px solid #000;
            padding: 40px;
            border-radius: 8px;
        }
        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .meta-table th {
            width: 30%;
            background-color: #f2f2f2 !important;
            border: 1px solid #000 !important;
        }
        .meta-table td, .items-table th, .items-table td {
            border: 1px solid #000 !important;
        }
        .signature-box {
            border-top: 1px dashed #000;
            margin-top: 50px;
            padding-top: 10px;
            text-align: center;
        }
        @media print {
            body {
                padding: 0;
            }
            .manifest-container {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- زر للطباعة اليدوية يختفي تلقائياً عند الطباعة الفعلية -->
    <div class="container text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-dark px-4 py-2 fw-bold">
            <i class="fas fa-print me-2"></i>Print This Manifest
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 ms-2">Close Window</button>
    </div>

    <div class="container">
        <div class="manifest-container">
            
            <!-- الهيدر الرسمي وشعار المنصة اللوجستية -->
            <div class="text-center mb-5 border-bottom pb-4">
                <h2 class="header-title mb-1">ReliefFlow Logistics Network</h2>
                <h5 class="text-muted text-uppercase mb-3">Official Cargo Dispatch Manifest</h5>
                <p class="mb-0 fs-7">UN / WFP / INGO Standardized Gate Pass & Delivery Slip</p>
            </div>

            <!-- جدول البيانات الفنية واللوجستية للشحنة -->
            <div class="row g-4 mb-5">
                <div class="col-8">
                    <h5 class="fw-bold mb-3">MANIFEST INFORMATION:</h5>
                    <table class="table meta-table table-bordered align-middle">
                        <tr>
                            <th>Manifest ID (QR Token)</th>
                            <td class="font-monospace fw-bold">{{ $shipment->qr_code_token }}</td>
                        </tr>
                        <tr>
                            <th>Dispatched From Depot</th>
                            <td>{{ $shipment->warehouse->name }} ({{ $shipment->warehouse->location }})</td>
                        </tr>
                        <tr>
                            <th>Target Destination</th>
                            <td class="fw-bold text-danger">{{ $shipment->aidRequest->location }}</td>
                        </tr>
                        <tr>
                            <th>Assigned Driver</th>
                            <td>{{ $shipment->driver_name }} (Phone: {{ $shipment->driver_phone }})</td>
                        </tr>
                        <tr>
                            <th>Dispatch Date & Time</th>
                            <td>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <!-- كود الـ QR مدمج وحي في الوثيقة الورقية للمسح الميداني السريع -->
                <div class="col-4 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3 border border-dark rounded">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $shipment->qr_code_token }}" alt="QR Code" class="img-fluid" style="width: 130px; height: 130px;">
                    </div>
                    <small class="text-muted font-monospace mt-2 d-block">SCAN FOR VERIFICATION</small>
                </div>
            </div>

            <!-- جدول تفاصيل المواد المحملة والكميات المصروفة -->
            <div class="mb-5">
                <h5 class="fw-bold mb-3">LOADED CARGO DETAILS (جدول المواد المحملة):</h5>
                <table class="table items-table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%">Item ID</th>
                            <th scope="col" style="width: 35%">Item Description</th>
                            <th scope="col" style="width: 20%">Category</th>
                            <th scope="col" style="width: 20%">Quantity Loaded</th>
                            <th scope="col" style="width: 15%">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipment->aidRequest->requestItems as $reqItem)
                        <tr>
                            <td>{{ $reqItem->item->id }}</td>
                            <td class="text-start fw-bold">{{ $reqItem->item->name }}</td>
                            <td>{{ $reqItem->item->category }}</td>
                            <td class="fw-bold fs-5">{{ number_format($reqItem->quantity) }}</td>
                            <td><span class="badge bg-dark px-2 py-1">{{ $reqItem->item->unit }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- قسم التوقيعات الرسمية في ذيل الوثيقة للاستلام والتسليم -->
            <div class="row g-4 mt-5 pt-4">
                <div class="col-4">
                    <div class="signature-box">
                        <p class="fw-bold mb-1 text-uppercase">Dispatched By</p>
                        <small class="text-muted">Warehouse Officer Signature</small>
                        <p class="mt-4 mb-0 font-monospace fs-7">Yousef Al Khateeb</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="signature-box">
                        <p class="fw-bold mb-1 text-uppercase">Carrier Acceptance</p>
                        <small class="text-muted">Driver Signature & Date</small>
                        <p class="mt-4 mb-0 font-monospace fs-7">{{ $shipment->driver_name }}</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="signature-box">
                        <p class="fw-bold mb-1 text-uppercase">Cargo Received By</p>
                        <small class="text-muted">Field Coordinator Signature</small>
                        <p class="mt-4 mb-0 font-monospace fs-7">____________________</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- أمر برمجيات لتشغيل خيار طباعة المتصفح فور فتح النافذة تلقائياً -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>