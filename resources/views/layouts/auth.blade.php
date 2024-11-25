<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory Management System</title>

     <!-- Add the favicon link here -->
     <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .brand-logo {
            font-size: 2rem;
            color: #2d3748;
        }
        .brand-title {
            font-weight: 600;
            color: #2d3748;
            letter-spacing: -0.5px;
        }
        .brand-subtitle {
            font-size: 0.95rem;
            color: #718096;
        }
        .auth-card input {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .auth-card input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-primary {
            background-color: #667eea;
            border-color: #667eea;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #5a67d8;
            border-color: #5a67d8;
            transform: translateY(-1px);
        }
        /* Preloader styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }
        .preloader-content {
            text-align: center;
        }
        .inventory-icon {
            width: 80px;
            height: 80px;
            position: relative;
            margin: 0 auto 20px;
        }
        .inventory-box {
            width: 20px;
            height: 20px;
            background-color: #667eea;
            position: absolute;
            animation: rotateBox 2s infinite ease-in-out;
        }
        .inventory-box:nth-child(1) {
            top: 0;
            left: 0;
            animation-delay: -1.5s;
        }
        .inventory-box:nth-child(2) {
            top: 0;
            right: 0;
            animation-delay: -1s;
        }
        .inventory-box:nth-child(3) {
            bottom: 0;
            left: 0;
            animation-delay: -0.5s;
        }
        .inventory-box:nth-child(4) {
            bottom: 0;
            right: 0;
            animation-delay: 0s;
        }
        @keyframes rotateBox {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(90deg); }
            50% { transform: rotate(180deg); }
            75% { transform: rotate(270deg); }
        }
        .loading-text {
            font-size: 1.2rem;
            color: #667eea;
            margin-top: 10px;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-content">
            <div class="inventory-icon">
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
            </div>
            <div class="loading-text">Loading Inventory System</div>
        </div>
    </div>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="auth-card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-boxes brand-logo mb-3 text-primary"></i>
                        <h1 class="h4 brand-title mb-2">IMS</h1>
                        <p class="brand-subtitle mb-0">Inventory Management System</p>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.querySelector('.preloader');
            const loadingText = document.querySelector('.loading-text');

            let dots = '';
            let dotCount = 0;
            // Animate loading text
            const textInterval = setInterval(() => {
                dotCount = (dotCount + 1) % 4;
                dots = '.'.repeat(dotCount);
                loadingText.textContent = `Loading Inventory System${dots}`;
            }, 300);

            // Hide preloader after animation completes
            setTimeout(() => {
                clearInterval(textInterval);
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
            }, 2000);
        });
    </script>
</body>
</html>