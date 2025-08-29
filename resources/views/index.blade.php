<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - KMI SAP Transfer</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #116ed1;
            --dark-blue: #0a4a9b;
            --text-dark: #212529;
            --text-secondary: #6c757d;
            --background-light: #f8f9fa;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }
        .split-screen {
            display: flex;
            width: 100%;
            height: 100%;
        }
        .split-screen > div {
            width: 50%;
            height: 100%;
        }
        .left-pane {
            /* ## PERBAIKAN: Menggunakan helper 'asset' Laravel untuk path gambar ## */
            background-image: url("{{ asset('images/forklift-transporting.jpg') }}");
            background-size: cover;
            background-position: center;
        }
        .right-pane {
            background-color: var(--background-light);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 4rem;
        }
        .content-wrapper {
            max-width: 450px;
            text-align: center;
        }
        .company-logo {
            height: 60px;
            margin-bottom: 2rem;
        }
        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .cta-button:hover {
            background-color: var(--dark-blue);
            transform: translateY(-3px);
        }
        .footer {
            font-size: 0.9rem;
            color: #aaa;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            .split-screen {
                flex-direction: column;
            }
            .split-screen > div {
                width: 100%;
            }
            .left-pane {
                height: 40%;
            }
            .right-pane {
                height: 60%;
                justify-content: center;
                padding: 2rem;
            }
            h1 {
                font-size: 2.2rem;
            }
            .footer {
                position: absolute;
                bottom: 1rem;
                color: var(--text-secondary);
            }
        }
    </style>
</head>
<body>
    <div class="split-screen">
        <div class="left-pane"></div>
        <div class="right-pane">
            <div class="content-wrapper">
                {{-- ## PERBAIKAN: Menggunakan helper 'asset' untuk path logo ## --}}
                <img src="{{ asset('images/KMI.png') }}" alt="KMI Logo" class="company-logo">
                <h1>HU Transfer System</h1>
                <p>Aplikasi untuk mempermudah proses perpindahan Handling Unit antar Storage Location di SAP.</p>
                {{-- ## PERBAIKAN: Menggunakan helper 'route' untuk link login ## --}}
                <a href="{{ route('login.form') }}" class="cta-button">Mulai Sekarang →</a>
            </div>
            <footer class="footer">
                {{-- ## PERBAIKAN: Membuat tahun menjadi dinamis ## --}}
                © PT. Kayu Mebel Indonesia, {{ date('Y') }}
            </footer>
        </div>
    </div>
</body>
</html>