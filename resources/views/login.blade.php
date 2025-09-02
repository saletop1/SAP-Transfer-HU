{{-- resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAP HU Transfer - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #212529;
            --secondary-dark: #343a40;
            --accent-blue: #3a86ff;
            --accent-blue-dark: #2c6ac4;
            --white: #FFFFFF;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --border-color: rgba(255, 255, 255, 0.2);
            --input-bg-color: rgba(255, 255, 255, 0.1);
            /* ## PERUBAHAN: Warna autofill disesuaikan dengan gradasi abu-abu ## */
            --autofill-bg: #212529;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            color: var(--white);

            /* ## PERUBAIKAN: Mengganti gradasi biru menjadi gradasi abu-abu gelap ## */
            background-image: linear-gradient(135deg, rgba(33, 37, 41, 0.85), rgba(52, 58, 64, 0.85)), url("{{ asset('images/forklift-transporting.jpg') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;

            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;

            /* ## PERUBAIKAN: Warna container disesuaikan dengan tema abu-abu ## */
            background-color: rgba(33, 37, 41, 0.6);
            backdrop-filter: blur(2,5px);
            -webkit-backdrop-filter: blur(7px);

            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .kmi-logo {
            height: 80px;
            margin-bottom: 1rem;
        }

        .header h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        label {
            position: absolute;
            top: 12px;
            left: 0;
            font-size: 1rem;
            color: var(--text-secondary);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        input {
            width: 100%;
            padding: 12px 0;
            font-size: 1rem;
            color: var(--white);
            background-color: transparent;
            border: none;
            border-bottom: 2px solid var(--border-color);
            outline: none;
            transition: border-color 0.3s ease;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-text-fill-color: var(--white) !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        input:focus ~ label,
        input:not(:placeholder-shown) ~ label,
        input:-webkit-autofill ~ label {
            top: -20px;
            left: 0;
            font-size: 0.8rem;
            color: var(--accent-blue);
        }

        input:focus {
            border-bottom-color: var(--accent-blue);
        }

        .login-link-wrapper {
            margin-top: 2.5rem;
            text-align: center;
        }

        .login-link {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease, background-color 0.3s ease;
            border-radius: 8px;
            cursor: pointer;
        }

        .login-link:hover {
            color: var(--white);
            background-color: rgba(58, 134, 255, 0.2);
        }

        #message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: none;
            text-align: left;
        }
        .error {
            display: block;
            background-color: rgba(220, 53, 69, 0.1);
            color: #f08080;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        .success {
            display: block;
            background-color: rgba(58, 134, 255, 0.1);
            color: #87cefa;
            border: 1px solid rgba(58, 134, 255, 0.3);
        }
        .footer {
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <img src="{{ asset('images/KMI.png') }}" alt="KMI Logo" class="kmi-logo">
            <h2>Selamat Datang Kembali</h2>
            <p>Login untuk melanjutkan ke sistem transfer</p>
        </div>

        <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <input type="text" id="username" name="username" required placeholder=" ">
                <label for="username">Username</label>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>
        </form>

        <div class="login-link-wrapper">
            <a id="loginSubmitLink" class="login-link">Login</a>
        </div>

        <div id="message"></div>
    </div>

    <footer class="footer">
        © PT. Kayu Mebel Indonesia, {{ date('Y') }}
    </footer>

    <script>
        document.getElementById('loginSubmitLink').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('loginForm');
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const messageEl = document.getElementById('message');

            messageEl.textContent = 'Mencoba login...';
            messageEl.className = 'success';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ username, password })
                });

                const result = await response.json();

                if (response.ok) {
                    window.location.href = result.redirect_url;
                } else {
                    messageEl.textContent = 'Login Gagal: ' + (result.message || 'Username atau password salah.');
                    messageEl.className = 'error';
                }
            } catch (error) {
                messageEl.textContent = 'Error: Tidak dapat terhubung ke server.';
                messageEl.className = 'error';
                console.error('Login error:', error);
            }
        });
    </script>
</body>
</html>

