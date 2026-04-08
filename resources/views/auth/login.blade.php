<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, rgba(255,255,255,0.08), transparent 55%), var(--bg-gradient);
            padding: 1.5rem;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(14px);
            border-radius: 18px;
            box-shadow: 0 16px 45px rgba(15, 61, 46, 0.35);
            padding: 1.5rem 1.75rem;
            width: 100%;
            max-width: 680px;
            animation: fadeInUp 0.6s ease-out;
            display: flex;
            gap: 1.5rem;
        }

        .login-card-left {
            flex: 1.1;
            padding: 1.25rem 1.1rem;
            border-radius: 14px;
            background: linear-gradient(145deg, rgba(95,179,142,0.13), rgba(15,61,46,0.08));
            border: 1px solid rgba(95,179,142,0.22);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .login-header {
            text-align: right;
            margin-bottom: 0.75rem;
        }
        
        .login-header .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: var(--bg-gradient);
            border-radius: 50%;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 24px rgba(95, 179, 142, 0.35);
        }
        
        .login-header .logo i {
            font-size: 2rem;
            color: white;
        }
        
        .login-header h1 {
            font-size: 1.4rem;
            font-weight: 900;
            color: rgba(15, 61, 46, 0.95);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: rgba(15, 61, 46, 0.7);
            font-size: 0.85rem;
        }

        .login-subtext {
            color: rgba(15, 61, 46, 0.65);
            font-size: 0.78rem;
            line-height: 1.7;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.9);
            color: rgba(15, 61, 46, 0.8);
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(255,255,255,0.9);
        }

        .login-badge i {
            color: var(--primary-color);
        }

        .login-card-right {
            flex: 1;
        }
        
        .form-group {
            margin-bottom: 1.1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(15, 61, 46, 0.9);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.85rem 0.9rem;
            border: 2px solid rgba(15, 61, 46, 0.1);
            border-radius: 10px;
            font-size: 0.95rem;
            color: rgba(15, 61, 46, 0.9);
            background: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
            font-family: 'Cairo', sans-serif;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(95, 179, 142, 0.1);
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        
        .form-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-checkbox label {
            color: rgba(15, 61, 46, 0.8);
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: var(--bg-gradient);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Cairo', sans-serif;
            box-shadow: 0 4px 14px rgba(95, 179, 142, 0.32);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(95, 179, 142, 0.42);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid rgba(220, 53, 69, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #dc3545;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-error i {
            font-size: 1.2rem;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                padding: 1.5rem 1.25rem;
                max-width: 380px;
                gap: 1.25rem;
            }

            .login-card-left {
                padding: 1rem 0.9rem;
            }
            
            .login-header h1 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
@php
    try {
        $siteSettings = \App\Models\SiteSetting::getAllAsArray();
    } catch (\Throwable $e) {
        $siteSettings = [];
    }
@endphp
    <div class="login-container">
        <div class="login-card">
            <div class="login-card-left">
                <div class="login-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ $siteSettings['site_title'] ?? $siteSettings['site_name'] ?? config('app.name', 'نظام وصال') }}</span>
                </div>
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1>تسجيل الدخول</h1>
                    <p>دخول آمن للوصول إلى لوحة التحكم</p>
                </div>
            </div>

            <div class="login-card-right">
                @if($errors->any())
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope"></i> البريد الإلكتروني
                        </label>
                        <div class="form-input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                value="{{ old('email') }}" 
                                placeholder="أدخل بريدك الإلكتروني"
                                required 
                                autofocus
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> كلمة المرور
                        </label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="أدخل كلمة المرور"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="form-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

