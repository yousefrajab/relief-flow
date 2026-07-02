<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReliefFlow | Secure Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Logo and Header -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-box-open text-primary me-2"></i>ReliefFlow</h2>
            <p class="text-muted">Humanitarian Logistics Portal</p>
        </div>

        <hr class="mb-4">

        <!-- نموذج تسجيل الدخول الآمن -->
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <!-- البريد الإلكتروني -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control py-2 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@reliefflow.com">
                </div>
                @error('email')
                    <div class="text-danger mt-1 fw-semibold fs-7"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- كلمة المرور -->
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control py-2 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                </div>
                @error('password')
                    <div class="text-danger mt-1 fw-semibold fs-7"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- التذكر وتأكيد الإرسال -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted fs-7" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill shadow-sm"><i class="fas fa-sign-in-alt me-2"></i>Access Portal</button>
        </form>

        <!-- معلومات الدخول السريعة للاختبار السريع (KPI Guide) -->
        <div class="mt-4 p-3 bg-light rounded text-center">
            <h6 class="fw-bold mb-2 text-dark fs-7">Quick Test Accounts (Password: <span class="font-monospace">password</span>):</h6>
            <div class="text-start fs-7 text-secondary">
                <span class="d-block">🛡️ <strong>Admin:</strong> admin@reliefflow.com</span>
                <span class="d-block">📦 <strong>Manager:</strong> manager@reliefflow.com</span>
                <span class="d-block">📍 <strong>Coordinator:</strong> coordinator@reliefflow.com</span>
            </div>
        </div>
    </div>

</body>
</html>