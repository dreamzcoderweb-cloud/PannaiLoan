<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <!-- Bootstrap core CSS -->
    <link href="{{url ('login_assets/assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .login-header p {
            font-size: 14px;
            color: #666;
        }
        .form-floating > .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-floating > .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
        .alert {
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h1>Reset Password</h1>
        <p>Enter your new password below</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="post">
        @csrf
        
        <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="New Password" required autofocus>
            <label for="password">New Password</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
            <label for="password_confirmation">Confirm Password</label>
        </div>

        <button class="w-100 btn btn-lg btn-primary btn-login" type="submit">Reset Password</button>
    </form>
</div>

</body>
</html>
