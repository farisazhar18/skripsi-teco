<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Terminal Coffee POS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #183f37, #2e5a4f);
            font-family: Arial, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #f5efe6;
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.25);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-brand img {
            width: 95px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 15px auto;
        }

        .login-brand h1 {
            margin: 0;
            color: #183f37;
            font-size: 28px;
            font-weight: bold;
        }

        .login-brand p {
            margin-top: 6px;
            color: #6b6256;
            font-size: 14px;
        }

        label {
            color: #183f37 !important;
            font-weight: bold;
        }

        input {
            border-radius: 12px !important;
            border: 1px solid #c9bca8 !important;
            padding: 12px !important;
        }

        input:focus {
            border-color: #183f37 !important;
            box-shadow: 0 0 0 3px rgba(24,63,55,0.15) !important;
        }

        .login-button {
            background: #183f37;
            color: #efe6d8;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: bold;
            border: none;
        }

        .login-button:hover {
            background: #2e5a4f;
        }

        a {
            color: #183f37 !important;
            font-weight: bold;
        }
    </style>

    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <img src="{{ asset('logo-terminal.png') }}" alt="Terminal Coffee">
                <h1>Terminal Coffee</h1>
                <p>POS Management System</p>
            </div>

            {{ $slot }}
        </div>
    </div>

    <script>
function togglePassword() {

    const password = document.getElementById('password');
    const eye = document.getElementById('eye-icon');

    if (password.type === 'password') {

        password.type = 'text';

        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');

    } else {

        password.type = 'password';

        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');

    }
}
</script>

</body>
</html>