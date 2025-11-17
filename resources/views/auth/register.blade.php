<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0C1A2A;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .register-card {
      background-color: #1E3A5F;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.1);
      width: 100%;
      max-width: 600px;
      padding: 2rem;
    }
    
    .register-header h4 {
      color: white;
      font-weight: 700;
      margin-bottom: 1rem;
    }
    
    .form-label {
      color: white;
      font-weight: 500;
    }
    
    .form-control {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      color: white;
      padding: 0.75rem 1rem;
    }
    
    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: #4A90E2;
      box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
      color: white;
    }
    
    .btn-register {
      background-color: #4A90E2;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      padding: 0.75rem;
      transition: all 0.3s;
    }
    
    .btn-register:hover {
      background-color: #3a7bc8;
      transform: translateY(-2px);
    }
    
    .login-link {
      color: #4A90E2;
      font-weight: 600;
      text-decoration: none;
    }
    
    .login-link:hover {
      color: #3a7bc8;
      text-decoration: underline;
    }
    
    .text-center {
      color: #E8F1FF;
    }
  </style>
</head>
<body>
  <div class="register-card">
    <div class="register-header">
      <h4>Create account</h4>
    </div>
    <form method="POST" action="{{ url('/register') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full name</label>
          <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
        </div>
        <div class="col-12">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
      </div>
      <button class="btn btn-register w-100 mt-3">Register</button>
    </form>
    <div class="text-center mt-3">
      <a href="/login" class="login-link">Already have an account?</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>