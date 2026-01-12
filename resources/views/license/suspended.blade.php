<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Suspended - MediBill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #78350f 0%, #451a03 100%);
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
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            color: #fcd34d;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
        .contact-info {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            color: #94a3b8;
        }
        .contact-info a {
            color: #6366f1;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="license-card text-center">
        <div class="icon-wrapper">
            <i class="fas fa-ban"></i>
        </div>
        
        <h1>License Suspended</h1>
        <p class="text-muted mb-4">
            Your MediBill license has been temporarily suspended. 
            This may be due to payment issues or a policy violation.
        </p>

        <div class="contact-info mb-4">
            <h6 class="text-white mb-3">Contact Support to Resolve</h6>
            <p class="mb-2">
                <i class="fas fa-envelope me-2"></i>
                <a href="mailto:support@medibill.com">support@medibill.com</a>
            </p>
            <p class="mb-0">
                <i class="fas fa-phone me-2"></i>
                <a href="tel:+911234567890">+91 1234-567-890</a>
            </p>
        </div>

        <p class="text-muted small mb-4">
            Please have your account details ready when contacting support.
            @if($license)
            <br>License: <code>{{ Str::limit($license->license_key, 12, '...') }}</code>
            @endif
        </p>

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
