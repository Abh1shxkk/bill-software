<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Billing Software</title>
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

        /* Left Panel - Visual (Moved to Left) */
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
            background: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                        radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
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
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: var(--mesh-3);
            bottom: 20%;
            left: 10%;
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

        /* Right Panel - Form (Moved to Right) */
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
        }

        .form-content {
            width: 100%;
            max-width: 400px;
        }

        .logo-area {
            margin-bottom: 3rem;
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
            margin-bottom: 3rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Premium Input Fields */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
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
            font-size: 1rem;
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
            font-size: 1.1rem;
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
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
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

        /* Error Message */
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .auth-form-side { width: 100%; padding: 2rem; }
            .auth-visual-side { display: none; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Visual Side (Moved to Left) -->
        <div class="auth-visual-side">
            <div class="mesh-gradient"></div>
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            
            <div class="glass-card">
                <h2>Streamline Your Business</h2>
                <p>Experience the next generation of billing software. Powerful, secure, and beautiful.</p>
            </div>
        </div>

        <!-- Form Side (Moved to Right) -->
        <div class="auth-form-side">
            <div class="form-content">
                <div class="logo-area">
                    <img src="{{ asset('https://res.cloudinary.com/dz8p5iadt/image/upload/v1766408874/m-white-logo-01_mxmf6y.svg') }}" alt="Medi BillSuite" style="height: 40px;">
                    <span class="logo-text">Medi-BillSuite</span>
                </div>

                <h1>Welcome back</h1>
                <p class="subtitle">Please enter your details to sign in.</p>

                @if(session('error'))
                    <div class="error-msg">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.perform') }}">
                    @csrf
                    
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
                            <label class="form-label">Username or Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('username')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
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
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-footer" style="margin-top: 0; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--accent-primary);">
                            Remember me
                        </label>
                        <a href="/forgot-password" class="link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        Sign In
                    </button>

                    <div class="auth-footer" style="justify-content: center; gap: 0.5rem;">
                        <span style="color: var(--text-secondary)">Don't have an account?</span>
                        <a href="{{ route('register.organization.form') }}" class="link highlight">Register Organization</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>