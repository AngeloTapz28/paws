<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — PAWS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: #dee2e6;
            letter-spacing: -4px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center px-4">
        <div class="error-code">@yield('code')</div>
        <i class="bi @yield('icon') @yield('icon-color') fs-1 mb-3"></i>
        <h2 class="fw-bold mb-2">@yield('title')</h2>
        <p class="text-muted mb-4" style="max-width:400px;margin:auto">@yield('message')</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn btn-primary px-4">
                <i class="bi bi-house me-1"></i> Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Go Back
            </a>
        </div>
        <div class="mt-5">
            <img src="{{ asset('images/paws-logo.png') }}" alt="PAWS" height="40"
                 onerror="this.style.display='none'">
        </div>
    </div>
</body>
</html>