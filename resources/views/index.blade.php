{{-- resources/views/index.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - KMI SAP Transfer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">

    <style>
        /* ## PERUBAHAN: Palet warna diubah menjadi tema gelap (charcoal) ## */
        :root {
            --primary-dark: #212529; /* Dark Charcoal */
            --secondary-dark: #343a40;
            --white: #d1d1d1ff;
            --text-secondary: rgba(255, 255, 255, 0.8);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            line-height: 1.7;
            color: var(--white);

            /* ## PERUBAHAN: Latar belakang gambar dengan overlay gelap, bukan ungu ## */
            background-image: linear-gradient(45deg, rgba(33, 37, 41, 0.85), rgba(52, 58, 64, 0.85)), url("{{ asset('images/forklift-transporting.jpg') }}");
            background-size: cover;
            background-position: center;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }

        .main-header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .company-logo-header {
            height: 70px;
        }

        .content-wrapper {
            max-width: 600px;
            width: 100%;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--white);
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        p {
            font-size: clamp(1rem, 3vw, 1.15rem);
            margin-bottom: 3rem;
            color: var(--text-secondary);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 35px;
            background-color: transparent;
            color: var(--white);
            border: 2px solid var(--white);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 50px; /* Pill shape */
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .cta-button:hover {
            background-color: var(--white);
            /* ## PERUBAHAN: Warna teks saat hover disesuaikan dengan tema gelap ## */
            color: var(--primary-dark);
        }

        .footer {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            h1 {
                margin-bottom: 1.5rem;
            }
            p {
                margin-bottom: 2.5rem;
            }
            .main-header {
                padding: 1rem;
                justify-content: center; /* Logo di tengah pada mobile */
            }
            .footer {
                position: relative; /* Footer mengalir normal di mobile */
                margin-top: 4rem;
            }
        }

    </style>
</head>
<body>
    <header class="main-header">
        <img src="{{ asset('images/KMI.png') }}" alt="KMI Logo" class="company-logo-header">
        <!-- Anda bisa menambahkan link navigasi di sini jika perlu -->
    </header>

    <div class="content-wrapper">
        <h1>HU Transfer System</h1>
        <p>Aplikasi untuk mempermudah proses perpindahan Handling Unit antar Storage Location di SAP.</p>
        <a href="{{ route('login.form') }}" class="cta-button">Mulai Sekarang</a>
    </div>

    <footer class="footer">
        © PT. Kayu Mebel Indonesia, {{ date('Y') }}
    </footer>
</body>
</html>

