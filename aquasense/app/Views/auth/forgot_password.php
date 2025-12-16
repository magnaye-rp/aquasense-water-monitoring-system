<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2d5a5a 0%, #1a3a3a 50%, #0f2626 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        /* Animated background elements */
        .bg-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }

        .water-bubble {
            position: absolute;
            background: radial-gradient(circle at 30% 30%, rgba(78, 172, 155, 0.15), transparent);
            border-radius: 50%;
            opacity: 0.5;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(10px); }
        }

        @keyframes glow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .glow-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, transparent, #4eac9b, transparent);
            animation: glow 3s infinite;
        }

        @keyframes rise {
            0% {
                opacity: 0;
                transform: translateY(100vh) translateX(0);
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translateY(-100vh) translateX(100px);
            }
        }

        .bubble {
            position: absolute;
            bottom: 0;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(159, 219, 205, 0.4), rgba(78, 172, 155, 0.1));
            border: 1px solid rgba(78, 172, 155, 0.2);
            animation: rise linear infinite;
        }

        .bubble::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 15%;
            width: 30%;
            height: 30%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.8), transparent);
            border-radius: 50%;
        }

        /* Forgot Password Card */
        .forgot-card {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.2) 100%);
            border: 2px solid #4eac9b;
            border-radius: 20px;
            padding: 45px;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), inset 0 0 30px rgba(78, 172, 155, 0.05);
            animation: slideInUp 0.8s ease-out;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .forgot-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(78, 172, 155, 0.1), transparent);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .forgot-card > * {
            position: relative;
            z-index: 2;
        }

        .brand {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(78, 172, 155, 0.3);
        }

        .brand-icon {
            font-size: 3.5rem;
            color: #4eac9b;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
            display: block;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .brand h1 {
            color: #4eac9b;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .brand p {
            color: #b0c4be;
            margin-top: 5px;
            font-size: 0.9rem;
        }

        /* Instructions */
        .instructions {
            background: rgba(78, 172, 155, 0.15);
            border: 1px solid rgba(78, 172, 155, 0.4);
            border-left: 4px solid #4eac9b;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #b0c4be;
        }

        .instructions i {
            color: #ffd700;
            margin-right: 10px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(78, 172, 155, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            color: white;
            font-size: 1rem;
            transition: all 0.4s;
        }

        .form-control::placeholder {
            color: rgba(176, 196, 190, 0.5);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #4eac9b;
            box-shadow: 0 0 0 0.3rem rgba(78, 172, 155, 0.25), inset 0 0 10px rgba(78, 172, 155, 0.1);
            color: white;
            outline: none;
        }

        .form-control:hover {
            border-color: #4eac9b;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.3rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            color: #ff9999;
            display: block;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Button */
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.4s;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(78, 172, 155, 0.3);
        }

        .btn-reset::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-reset:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(78, 172, 155, 0.4);
        }

        .btn-reset:hover:not(:disabled)::before {
            left: 100%;
        }

        .btn-reset:active:not(:disabled) {
            transform: translateY(-1px);
        }

        .btn-reset:disabled {
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* Links */
        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(78, 172, 155, 0.2);
        }

        .links a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }

        .links a:hover {
            color: #4eac9b;
            text-decoration: underline;
            transform: translateX(-3px);
        }

        .links i {
            transition: transform 0.3s;
        }

        .links a:hover i {
            transform: translateX(-5px);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: 1px solid;
            margin-bottom: 20px;
            padding: 12px 16px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.5);
            color: #ff9999;
        }

        .alert-success {
            background: rgba(78, 172, 155, 0.15);
            border-color: rgba(78, 172, 155, 0.5);
            color: #4eac9b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .forgot-card {
                padding: 35px;
                max-width: 100%;
            }

            .brand-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-elements">
        <div class="water-bubble" style="width: 300px; height: 300px; top: 10%; left: 5%; animation-delay: 0s;"></div>
        <div class="water-bubble" style="width: 250px; height: 250px; top: 60%; right: 10%; animation-delay: 2s;"></div>
        <div class="glow-line" style="top: 30%; left: 0; width: 100%; animation-delay: 0s;"></div>
        <div class="glow-line" style="top: 70%; left: 0; width: 100%; animation-delay: 1s;"></div>
        
        <!-- Floating bubbles -->
        <div class="bubble" style="width: 40px; height: 40px; left: 10%; animation-duration: 8s; animation-delay: 0s;"></div>
        <div class="bubble" style="width: 60px; height: 60px; left: 20%; animation-duration: 10s; animation-delay: 1s;"></div>
        <div class="bubble" style="width: 30px; height: 30px; left: 30%; animation-duration: 12s; animation-delay: 2s;"></div>
        <div class="bubble" style="width: 50px; height: 50px; left: 15%; animation-duration: 9s; animation-delay: 3s;"></div>
        <div class="bubble" style="width: 35px; height: 35px; left: 25%; animation-duration: 11s; animation-delay: 4s;"></div>
        <div class="bubble" style="width: 45px; height: 45px; left: 35%; animation-duration: 10s; animation-delay: 5s;"></div>
    </div>

    <div class="forgot-card">
        <div class="brand">
            <i class="fas fa-lock brand-icon"></i>
            <h1>Reset Password</h1>
            <p>AquaSense Water Monitoring</p>
        </div>

        <div class="instructions">
            <i class="fas fa-info-circle"></i>
            Enter your email address and we'll send you instructions to reset your password.
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><i class="fas fa-times-circle me-2"></i><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('forgot-password') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1" style="color: #ffd700;"></i> Email Address
                </label>
                <input type="email" 
                       class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                       id="email" 
                       name="email" 
                       value="<?= old('email') ?>" 
                       required 
                       placeholder="you@example.com">
                <?php if (session('errors.email')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.email') ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-reset">
                <i class="fas fa-paper-plane"></i> Send Reset Instructions
            </button>

            <div class="links">
                <a href="<?= base_url('login') ?>">
                    <i class="fas fa-arrow-left"></i> Back to Sign In
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add interactivity
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-reset');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;
        });
    </script>
</body>
</html>