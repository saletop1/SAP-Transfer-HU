{{-- resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- ## PERBAIKAN: Menambahkan CSRF Token untuk keamanan request AJAX ## --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>SAP HU Transfer - Login</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #007bff;
            --light-gray: #f4f7f6;
            --dark-gray: #333;
            --border-radius: 8px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .login-container {
            background-color: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }
        .header-title-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .header h2 {
            font-weight: 600;
            color: var(--dark-gray);
            margin: 0;
        }
        .kmi-logo {
            height: 35px;
        }
        .sap-logo {
            height: 30px;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            background-color: var(--primary-color);
            color: white;
        }
        button:hover {
            background-color: #0056b3;
        }
        #message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: none; /* Awalnya disembunyikan */
            text-align: left;
        }
        .error {
            display: block; /* Tampilkan jika ada error */
            background-color: #fceeee;
            color: #dc3545;
        }
        .success {
            display: block; /* Tampilkan jika sedang loading/sukses */
            background-color: #e6f7ff;
            color: #007bff;
        }
        .footer {
            margin-top: 2rem;
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <div class="header-title-group">
                {{-- ## PERBAIKAN: Menggunakan helper 'asset' untuk logo ## --}}
                <img src="{{ asset('images/KMI.png') }}" alt="KMI Logo" class="kmi-logo">
                <h2>SAP HU Transfer</h2>
            </div>
            <img src="https://www.sap.com/dam/application/shared/logos/sap-logo-svg.svg" alt="SAP Logo" class="sap-logo">
        </div>

        {{-- ## PERBAIKAN: Menggunakan helper 'route' untuk action dan menambahkan @csrf ## --}}
        <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username SAP" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit">Let's go!!</button>
        </form>
        <div id="message"></div>
    </div>

    <footer class="footer">
        {{-- ## PERBAIKAN: Membuat tahun menjadi dinamis ## --}}
        © PT. Kayu Mebel Indonesia, {{ date('Y') }}
    </footer>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah form submit cara biasa

            const form = event.target;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const messageEl = document.getElementById('message');
            
            // Tampilkan pesan loading
            messageEl.textContent = 'Mencoba login...';
            messageEl.className = 'success';

            try {
                // ## PERBAIKAN: Mengambil token CSRF dari meta tag ##
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken, // Mengirim token CSRF di header
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ username, password })
                });

                const result = await response.json();

                if (response.ok) {
                    // Jika sukses, backend akan mengirimkan URL redirect
                    window.location.href = result.redirect_url;
                } else {
                    // Menampilkan pesan error dari server
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