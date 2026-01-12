<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Required - MediBill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
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
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
            color: #f1f5f9;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
        .form-control {
            background: #0f172a;
            border: 1px solid #334155;
            color: #f1f5f9;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            letter-spacing: 2px;
            text-align: center;
            text-transform: uppercase;
        }
        .form-control:focus {
            background: #0f172a;
            border-color: #6366f1;
            color: #f1f5f9;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }
        .form-control::placeholder {
            color: #64748b;
            letter-spacing: 2px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .help-text {
            color: #64748b;
            font-size: 0.875rem;
        }
        .help-text a {
            color: #6366f1;
        }
    </style>
</head>
<body>
    <div class="license-card text-center">
        <div class="icon-wrapper">
            <i class="fas fa-key"></i>
        </div>
        
        <h1>License Required</h1>
        <p class="text-muted mb-4">
            Please enter your license key to activate MediBill for your organization.
        </p>

        @if(session('error'))
            <div class="alert mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form action="{{ route('license.activate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input type="text" name="license_key" class="form-control" 
                       placeholder="XXXX-XXXX-XXXX-XXXX"
                       value="{{ old('license_key') }}"
                       maxlength="19"
                       pattern="[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}"
                       required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-unlock me-2"></i>Activate License
            </button>
        </form>

        <p class="help-text mb-0">
            Don't have a license key? <a href="mailto:support@medibill.com">Contact Support</a>
        </p>

        <hr class="my-4 border-secondary">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-muted">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </button>
        </form>
    </div>

    <script>
        // Auto-format license key input
        document.querySelector('input[name="license_key"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            let formatted = '';
            for (let i = 0; i < value.length && i < 16; i++) {
                if (i > 0 && i % 4 === 0) formatted += '-';
                formatted += value[i];
            }
            e.target.value = formatted;
        });
    </script>
</body>
</html>
