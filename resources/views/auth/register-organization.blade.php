<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Organization - MediBill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .register-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .register-card {
            background: rgba(30, 41, 59, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .register-header h1 { font-size: 1.75rem; margin: 0; }
        .register-header p { margin: 0.5rem 0 0; opacity: 0.9; }
        .register-body { padding: 2rem; }
        .form-label { color: #94a3b8; font-size: 0.875rem; margin-bottom: 0.5rem; }
        .form-control, .form-select {
            background: #0f172a;
            border: 1px solid #334155;
            color: #f1f5f9;
            padding: 0.75rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            background: #0f172a;
            border-color: #6366f1;
            color: #f1f5f9;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }
        .form-control::placeholder { color: #64748b; }
        .section-title {
            color: #f1f5f9;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #334155;
        }
        .section-title i { color: #6366f1; margin-right: 0.5rem; }
        .plan-card {
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .plan-card:hover { border-color: #6366f1; }
        .plan-card.selected { border-color: #6366f1; background: rgba(99, 102, 241, 0.1); }
        .plan-card input { display: none; }
        .plan-name { color: #f1f5f9; font-weight: 600; }
        .plan-price { color: #6366f1; font-size: 1.25rem; font-weight: 700; }
        .plan-features { color: #94a3b8; font-size: 0.75rem; }
        .btn-register {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .text-muted { color: #64748b !important; }
        a { color: #6366f1; }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }
        .trial-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1><i class="fas fa-hospital-user me-2"></i>MediBill</h1>
                <p>Register Your Organization</p>
            </div>
            <div class="register-body">
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.organization') }}" method="POST">
                    @csrf

                    <!-- Plan Selection -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-crown"></i>Select Your Plan
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <label class="plan-card w-100 {{ old('plan_type', 'trial') == 'trial' ? 'selected' : '' }}">
                                    <input type="radio" name="plan_type" value="trial" 
                                           {{ old('plan_type', 'trial') == 'trial' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <span class="trial-badge">14 Days Free</span>
                                        <div class="plan-name mt-2">Trial</div>
                                        <div class="plan-price">₹0</div>
                                        <div class="plan-features">3 users, 500 items</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="plan-card w-100 {{ old('plan_type') == 'basic' ? 'selected' : '' }}">
                                    <input type="radio" name="plan_type" value="basic"
                                           {{ old('plan_type') == 'basic' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="plan-name">Basic</div>
                                        <div class="plan-price">₹999/mo</div>
                                        <div class="plan-features">5 users, 2000 items</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="plan-card w-100 {{ old('plan_type') == 'standard' ? 'selected' : '' }}">
                                    <input type="radio" name="plan_type" value="standard"
                                           {{ old('plan_type') == 'standard' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="plan-name">Standard</div>
                                        <div class="plan-price">₹2,499/mo</div>
                                        <div class="plan-features">15 users, 10K items</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="plan-card w-100 {{ old('plan_type') == 'premium' ? 'selected' : '' }}">
                                    <input type="radio" name="plan_type" value="premium"
                                           {{ old('plan_type') == 'premium' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="plan-name">Premium</div>
                                        <div class="plan-price">₹4,999/mo</div>
                                        <div class="plan-features">50 users, 50K items</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Organization Details -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-building"></i>Organization Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                                <input type="text" name="organization_name" class="form-control" 
                                       value="{{ old('organization_name') }}" required
                                       placeholder="Your Business Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="organization_email" class="form-control" 
                                       value="{{ old('organization_email') }}" required
                                       placeholder="business@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="organization_phone" class="form-control" 
                                       value="{{ old('organization_phone') }}"
                                       placeholder="+91 9876543210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">GST Number</label>
                                <input type="text" name="organization_gst_no" class="form-control" 
                                       value="{{ old('organization_gst_no') }}"
                                       placeholder="22AAAAA0000A1Z5">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="organization_address" class="form-control" 
                                       value="{{ old('organization_address') }}"
                                       placeholder="Street Address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="organization_city" class="form-control" 
                                       value="{{ old('organization_city') }}"
                                       placeholder="City">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="organization_state" class="form-control" 
                                       value="{{ old('organization_state') }}"
                                       placeholder="State">
                            </div>
                        </div>
                    </div>

                    <!-- Admin Account -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-user-shield"></i>Admin Account
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="admin_name" class="form-control" 
                                       value="{{ old('admin_name') }}" required
                                       placeholder="Your Full Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="admin_username" class="form-control" 
                                       value="{{ old('admin_username') }}" required
                                       placeholder="username">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="admin_email" class="form-control" 
                                       value="{{ old('admin_email') }}" required
                                       placeholder="your.email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="admin_password" class="form-control" 
                                       required minlength="8"
                                       placeholder="Min. 8 characters">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="admin_password_confirmation" class="form-control" 
                                       required placeholder="Confirm password">
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="agree_terms" class="form-check-input" 
                                   id="agreeTerms" value="1" {{ old('agree_terms') ? 'checked' : '' }} required>
                            <label class="form-check-label text-muted" for="agreeTerms">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-register btn-primary">
                            <i class="fas fa-rocket me-2"></i>Create Account & Start
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <span class="text-muted">Already have an account?</span>
                        <a href="{{ route('login') }}" class="ms-1">Sign In</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">
                &copy; {{ date('Y') }} MediBill. All rights reserved.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Plan card selection
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>
