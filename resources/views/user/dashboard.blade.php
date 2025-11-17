<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card mx-auto" style="max-width: 560px;">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-1">User Dashboard</h5>
          <div class="text-muted small">Logged in as {{ auth()->user()->full_name }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-primary">Logout</button>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


