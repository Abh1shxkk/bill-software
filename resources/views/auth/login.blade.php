<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Billing Software</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-bg: #0f0c29;
            --darker-bg: #0a0819;
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.18);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.15);
            --accent-color: #667eea;
            --accent-hover: #5568d3;
            --success-color: #10b981;
            --error-color: #ef4444;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--dark-bg);
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 1rem 0;
            overflow: hidden;
        }

        /* Interactive Mouse Background Effect */
        .mouse-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.15) 0%, transparent 70%);
            pointer-events: none;
            z-index: 1;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease-out;
            filter: blur(40px);
        }

        /* Animated Background Elements */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s infinite ease-in-out;
        }

        .bg-animation .shape-1 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            top: -10%;
            left: -10%;
            animation-delay: 0s;
        }

        .bg-animation .shape-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            bottom: -10%;
            right: -10%;
            animation-delay: 5s;
        }

        .bg-animation .shape-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            animation: particleFloat 15s infinite ease-in-out;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 12s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 14s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 13s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 16s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 11s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 15s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 12s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; animation-duration: 14s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 4.5s; animation-duration: 13s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 0.5s; animation-duration: 16s; }

        @keyframes particleFloat {
            0%, 100% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) scale(1);
            }
            90% {
                opacity: 1;
                transform: translateY(10vh) scale(1);
            }
            100% {
                transform: translateY(-10vh) scale(0);
                opacity: 0;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        /* Main Container */
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            padding: 2rem;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel - Branding */
        .brand-panel {
            padding: 2.5rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: pulse 3s ease-in-out infinite;
            position: relative;
        }

        .brand-logo::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 23px;
            background: var(--primary-gradient);
            z-index: -1;
            opacity: 0;
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% {
                opacity: 0;
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(1.1);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
            }
        }

        .brand-logo i {
            font-size: 2rem;
            color: white;
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.2;
            background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: default;
        }

        .feature-item:hover {
            transform: translateX(10px);
            color: var(--text-primary);
        }

        .feature-item:hover i {
            background: rgba(102, 126, 234, 0.4);
            transform: scale(1.1);
        }

        .feature-item i {
            width: 36px;
            height: 36px;
            background: rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--accent-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        /* Right Panel - Login Form */
        .form-panel {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            animation: fadeInDown 0.6s ease-out 0.2s both;
        }

        .form-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            animation: fadeInDown 0.6s ease-out 0.3s both;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.5s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.125rem;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: var(--input-bg);
            border: 2px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control.has-toggle {
            padding-right: 3rem;
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
            font-size: 1.125rem;
            transition: all 0.3s ease;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle:focus {
            outline: none;
            color: var(--accent-color);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:hover {
            border-color: rgba(102, 126, 234, 0.5);
            background: rgba(255, 255, 255, 0.07);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 0 20px rgba(102, 126, 234, 0.1);
        }

        .input-wrapper.focused .input-icon {
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            animation: fadeInUp 0.5s ease-out 0.3s both;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            cursor: pointer;
            accent-color: var(--accent-color);
            transition: all 0.3s ease;
        }

        .checkbox-wrapper input[type="checkbox"]:hover {
            transform: scale(1.1);
        }

        .checkbox-wrapper label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
        }

        .checkbox-wrapper:hover label {
            color: var(--text-primary);
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--accent-hover);
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        /* Button Styles with Animations */
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out 0.4s both;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--secondary-gradient);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .btn-login:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5), 0 0 40px rgba(102, 126, 234, 0.2);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover::after {
            opacity: 0.3;
        }

        .btn-login:active {
            transform: translateY(-1px) scale(0.98);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Ripple Effect */
        .btn-login .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .btn-login .btn-text {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login .btn-text i {
            transition: transform 0.3s ease;
        }

        .btn-login:hover .btn-text i {
            transform: translateX(5px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
            animation: fadeInUp 0.5s ease-out 0.5s both;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--input-border), transparent);
        }

        .divider span {
            padding: 0 1rem;
        }

        .register-text {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
            animation: fadeInUp 0.5s ease-out 0.6s both;
        }

        .register-link {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .register-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .register-link:hover {
            color: var(--accent-hover);
        }

        .register-link:hover::after {
            width: 100%;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .error-message i {
            margin-right: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                display: none;
            }

            .form-panel {
                padding: 3rem 2rem;
            }
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }

            .form-panel {
                padding: 2rem 1.5rem;
            }

            .brand-title {
                font-size: 2rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
        }

        /* Loading State */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading .btn-text {
            opacity: 0;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Mouse Glow Effect -->
    <div class="mouse-glow" id="mouseGlow"></div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left Panel - Branding -->
            <div class="brand-panel">
                <div class="brand-content">
                    <div class="brand-logo">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h1 class="brand-title">Professional Billing Software</h1>
                    <p class="brand-subtitle">Streamline your business operations with our comprehensive billing and invoice management system.</p>
                    
                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Advanced invoice management</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Real-time financial tracking</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Comprehensive reporting tools</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Secure data management</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Multi-user collaboration</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="form-panel">
                <div class="form-header">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Sign in to access your account</p>
                </div>

                <form method="POST" action="{{ route('login.perform') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">USERNAME OR EMAIL</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                name="username" 
                                value="{{ old('username') }}" 
                                class="form-control" 
                                placeholder="Enter your username or email" 
                                required
                                autocomplete="username"
                            >
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        @error('username')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">PASSWORD</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="password" 
                                id="passwordInput"
                                class="form-control has-toggle" 
                                placeholder="Enter your password" 
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="rememberMe" name="remember">
                            <label for="rememberMe">Remember me</label>
                        </div>
                        <a href="/forgot-password" class="forgot-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">
                            Sign In
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </button>
                    
                    <div class="divider">
                        <span>OR</span>
                    </div>
                    
                    <p class="register-text">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="register-link">Create one now</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mouse Glow Effect - Interactive background that follows cursor
        const mouseGlow = document.getElementById('mouseGlow');
        let mouseX = 0, mouseY = 0;
        let glowX = 0, glowY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animateGlow() {
            const dx = mouseX - glowX;
            const dy = mouseY - glowY;
            
            glowX += dx * 0.1;
            glowY += dy * 0.1;
            
            mouseGlow.style.left = glowX + 'px';
            mouseGlow.style.top = glowY + 'px';
            
            requestAnimationFrame(animateGlow);
        }
        animateGlow();

        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword && passwordInput && toggleIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        }

        // Ripple effect on button
        const loginBtn = document.getElementById('loginBtn');
        loginBtn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });

        // Add smooth focus transitions
        document.querySelectorAll('.form-control').forEach(input => {
            const wrapper = input.closest('.input-wrapper');
            
            input.addEventListener('focus', function() {
                wrapper.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                wrapper.classList.remove('focused');
            });
        });

        // Keyboard accessibility for Enter key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON' && document.activeElement.tagName !== 'A') {
                const form = document.getElementById('loginForm');
                if (form.contains(document.activeElement)) {
                    form.requestSubmit();
                }
            }
        });
    </script>
</body>
</html>