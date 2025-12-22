<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Billing Software</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Premium Dark Theme Palette */
            --bg-color: #030014;
            --surface-color: #0f0c29;
            --surface-hover: #1a1640;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --accent-primary: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.5);
            --border-color: rgba(255, 255, 255, 0.08);
            --input-bg: rgba(255, 255, 255, 0.03);
            
            /* Gradients */
            --mesh-1: #4f46e5;
            --mesh-2: #818cf8;
            --mesh-3: #c084fc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Split Layout */
        .auth-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Right Panel - Form (Swapped for Register) */
        .auth-form-side {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            position: relative;
            background: var(--bg-color);
            z-index: 10;
            overflow-y: auto; /* Handle smaller screens */
        }

        .form-content {
            width: 100%;
            max-width: 480px; /* Slightly wider for 2-col layout */
        }

        .logo-area {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--mesh-1), var(--mesh-3));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Premium Input Fields */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .input-wrapper {
            position: relative;
        }

        .custom-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fix for Autofill */
        .custom-input:-webkit-autofill,
        .custom-input:-webkit-autofill:hover, 
        .custom-input:-webkit-autofill:focus, 
        .custom-input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px var(--bg-color) inset !important;
            -webkit-text-fill-color: var(--text-primary) !important;
            transition: background-color 5000s ease-in-out 0s;
            caret-color: var(--text-primary);
        }

        .custom-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1rem;
            transition: color 0.3s;
            z-index: 2;
        }

        .custom-input:focus + .input-icon {
            color: var(--accent-primary);
        }

        .form-label {
            position: absolute;
            left: 3rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            padding: 0 0.2rem;
            font-size: 0.95rem;
            z-index: 1;
        }

        /* Floating label logic */
        .custom-input:focus ~ .form-label,
        .custom-input:not(:placeholder-shown) ~ .form-label,
        .custom-input:-webkit-autofill ~ .form-label {
            top: 0;
            left: 0.8rem;
            transform: translateY(-50%) scale(0.85);
            color: var(--accent-primary);
            background: var(--bg-color);
            padding: 0 0.4rem;
            z-index: 2;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.3s;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        /* Action Buttons */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, var(--mesh-1), var(--accent-primary));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -10px var(--accent-primary);
        }

        /* Shimmer Effect */
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.2), transparent);
            transform: skewX(-20deg);
            transition: 0.5s;
        }

        .btn-primary:hover::after {
            left: 150%;
            transition: 1s;
        }

        /* Extra Links */
        .auth-footer {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .link {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .link:hover {
            color: var(--text-primary);
        }

        .link.highlight {
            color: var(--accent-primary);
            font-weight: 500;
        }

        .link.highlight:hover {
            text-decoration: underline;
        }

        /* Left Panel - Visual (Swapped) */
        .auth-visual-side {
            width: 50%;
            position: relative;
            background: #000;
            overflow: hidden;
        }

        /* Interactive Mesh Gradient Background */
        .mesh-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(at 100% 100%, hsla(253,16%,7%,1) 0, transparent 50%), 
                        radial-gradient(at 0% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                        radial-gradient(at 0% 100%, hsla(339,49%,30%,1) 0, transparent 50%);
            filter: blur(80px);
            opacity: 0.6;
            animation: gradient-shift 10s ease infinite;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            animation: float 20s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: var(--mesh-1);
            top: -10%;
            left: -10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: var(--mesh-3);
            bottom: 10%;
            right: 10%;
            animation-delay: -5s;
        }

        .glass-card {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            color: white;
            text-align: center;
        }

        .glass-card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card p {
            color: #94a3b8;
            line-height: 1.6;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(30px, -50px); }
            66% { transform: translate(-20px, 20px); }
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Error Message */
        .error-msg {
            background: rgba(2EF, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .auth-form-side { width: 100%; padding: 2rem; }
            .auth-visual-side { display: none; }
            .form-grid { grid-template-columns: 1fr; gap: 0; }
            .form-group.full-width { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Visual Side (Left for Register) -->
        <div class="auth-visual-side">
            <div class="mesh-gradient"></div>
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            
            <div class="glass-card">
                <h2>Join the Future</h2>
                <p>Start managing your business with the most advanced tools available. Quick setup, powerful results.</p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-form-side">
            <div class="form-content">
                <div class="logo-area">
                    <div class="logo-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <span class="logo-text">Medi BillSuite</span>
                </div>

                <h1>Create Account</h1>
                <p class="subtitle">Join thousands of businesses growing with us.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    type="text" 
                                    name="full_name" 
                                    class="custom-input" 
                                    placeholder=" "
                                    value="{{ old('full_name') }}"
                                    required
                                >
                                <label class="form-label">Full Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            @error('full_name')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    type="text" 
                                    name="username" 
                                    class="custom-input" 
                                    placeholder=" "
                                    value="{{ old('username') }}"
                                    required
                                >
                                <label class="form-label">Username</label>
                                <i class="fas fa-at input-icon"></i>
                            </div>
                            @error('username')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <div class="input-wrapper">
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="custom-input" 
                                    placeholder=" "
                                    value="{{ old('email') }}"
                                    required
                                >
                                <label class="form-label">Email Address</label>
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                            @error('email')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    class="custom-input" 
                                    placeholder=" "
                                    required
                                >
                                <label class="form-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            @error('password')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    class="custom-input" 
                                    placeholder=" "
                                    required
                                >
                                <label class="form-label">Confirm Pass</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Get Started
                        <i class="fas fa-arrow-right" style="margin-left: 0.5rem; font-size: 0.9em;"></i>
                    </button>

                    <div class="auth-footer">
                        <span style="color: var(--text-secondary)">Already have an account?</span>
                        <a href="{{ route('login') }}" class="link highlight">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>