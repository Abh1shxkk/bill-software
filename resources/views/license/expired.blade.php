<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Expired - MediBill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .license-card {
            background: rgba(30, 41, 59, 0.95);
            border-radius: 20px;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrapper i {
            font-size: 2.5rem;
            color: white;
        }
        h1 {
            color: #fca5a5;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
        .info-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .info-box .label {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .info-box .value {
            color: #f1f5f9;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .expired-date {
            color: #ef4444;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: #1f2937;
        }
        .contact-info {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1rem;
            color: #94a3b8;
            font-size: 0.875rem;
        }
        .contact-info a {
            color: #6366f1;
        }
    </style>
</head>
<body>
    <div class="license-card text-center">
        <div class="icon-wrapper">
            <i class="fas fa-clock"></i>
        </div>
        
        <h1>License Expired</h1>
        <p class="text-muted mb-4">
            Your MediBill license has expired. Please renew to continue using the software.
        </p>

        @if($license)
        <div class="info-box">
            <div class="row">
                <div class="col-6 text-start">
                    <div class="label">License Key</div>
                    <div class="value">{{ Str::limit($license->license_key, 12, '...') }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="label">Expired On</div>
                    <div class="expired-date">{{ $license->expires_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="d-grid gap-2 mb-4">
            <a href="mailto:support@medibill.com?subject=License Renewal Request" class="btn btn-warning">
                <i class="fas fa-sync me-2"></i>Request License Renewal
            </a>
        </div>

        <div class="contact-info mb-4">
            <i class="fas fa-headset me-2"></i>
            Need help? Contact us at <a href="mailto:support@medibill.com">support@medibill.com</a>
            <br>or call <a href="tel:+911234567890">+91 1234-567-890</a>
        </div>

        <hr class="my-4 border-secondary">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-muted">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </button>
        </form>
    </div>
</body>
</html>
