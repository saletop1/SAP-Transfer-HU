{{-- resources/views/transfer.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transfer HU antar Sloc</title>

    {{-- Font dan Lottie Player --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm"></script>
    
    {{-- SweetAlert2 (masih digunakan untuk notifikasi error kamera/validasi) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #116ed1;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --error-color: #dc3545;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --text-black: #212529;
            --border-radius: 8px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark-gray);
            min-height: 100vh;
            display: flex;
            line-height: 1.6;
        }
        .split-screen { display: flex; width: 100%; }
        .split-screen > div { width: 50%; height: 100vh; }
        .left-pane {
            background-image: url("{{ asset('images/forklift-transporting.jpg') }}");
            background-size: cover;
            background-position: center;
        }
        .right-pane {
            background-color: var(--light-gray); display: flex; flex-direction: column;
            justify-content: center; align-items: center; padding: clamp(2rem, 8vw, 4rem);
            overflow-y: auto;
        }
        .card {
            background-color: white; padding: clamp(1.5rem, 6vw, 2.5rem);
            border-radius: var(--border-radius); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%; max-width: 480px; position: relative;
        }
        .header { text-align: center; margin-bottom: 2rem; }
        .header-main { display: flex; justify-content: center; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .sap-logo { height: 28px; }
        .header h2 { font-weight: 600; margin: 0; font-size: clamp(1.5rem, 5vw, 1.75rem); color: var(--text-black); }
        .header p { color: var(--secondary-color); }
        .form-group { margin-bottom: 1.5rem; }
        label, .hu-list-container h3 { display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 1rem; color: var(--text-black); }
        .input-wrapper { display: flex; align-items: center; gap: 0.75rem; }
        .input-wrapper input { flex-grow: 1; }
        .btn-icon { flex-shrink: 0; width: 48px; height: 48px; display: flex; justify-content: center; align-items: center; background-color: white; border: 1px solid #ddd; border-radius: var(--border-radius); cursor: pointer; transition: background-color 0.3s, border-color 0.3s; }
        .btn-icon:hover { background-color: #f8f9fa; border-color: #ccc; }
        .btn-icon svg { width: 24px; height: 24px; color: var(--secondary-color); }
        input[type="text"], select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem; transition: border-color 0.3s, box-shadow 0.3s; background-color: white; color: var(--dark-gray); }
        input[readonly] { background-color: #f8f9fa; cursor: default; }
        select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236c757d%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right .9em top 50%; background-size: .65em auto; color: var(--secondary-color); }
        select:valid { color: var(--text-black); }
        input[type="text"]:focus, select:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(17, 110, 209, 0.15); }
        .btn { display: flex; justify-content: center; align-items: center; gap: 0.75rem; width: 100%; padding: 12px; border: none; border-radius: var(--border-radius); font-size: 1rem; font-weight: 600; cursor: pointer; transition: background-color 0.3s, transform 0.2s; }
        .btn-primary { background-color: var(--primary-color); color: white; margin-top: 1rem; }
        .btn-primary:hover { background-color: #0d5ab8; transform: translateY(-2px); }
        .btn-primary img { width: 32px; height: 32px; }
        #logoutBtn { position: absolute; top: 0.75rem; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--secondary-color); padding: 0.5rem; }
        #logoutBtn:hover { color: var(--error-color); }
        #qr-reader { width: 100%; border-radius: var(--border-radius); overflow: hidden; margin-top: 1rem; }
        .loader-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.95); display: none; flex-direction: column; justify-content: center; align-items: center; z-index: 10; gap: 1rem; border-radius: var(--border-radius); }
        .loader-text { font-weight: 500; color: var(--dark-gray); }
        .hu-list-container { margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1.5rem; }
        #huList { list-style: none; max-height: 150px; overflow-y: auto; border: 1px solid #ddd; border-radius: var(--border-radius); padding: 0.5rem; }
        #huList li { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid #eee; color: var(--text-black); }
        #huList li:last-child { border-bottom: none; }
        .remove-btn { background: none; border: none; color: var(--error-color); cursor: pointer; font-weight: bold; font-size: 1.2rem; }
        .footer { text-align: center; color: #888; font-size: 0.9rem; margin-top: 2rem; }

        .notification {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .notification.is-success {
            background-color: #e9f7ef;
            color: var(--success-color);
        }
        .notification.is-error {
            background-color: #fceeee;
            color: var(--error-color);
        }
        .failed-hu-list {
            list-style-type: none;
            padding-left: 0;
            margin-top: 0.75rem;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 400;
        }
        .failed-hu-list li {
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .split-screen { flex-direction: column; }
            .split-screen > div { width: 100%; height: auto; }
            .left-pane { min-height: 200px; height: 30vh; }
            .right-pane { height: auto; justify-content: flex-start; padding: 2rem 1.5rem; }
            .card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>
    <div class="split-screen">
        <div class="left-pane"></div>
        <div class="right-pane">
            <div class="card">
                <div class="loader-overlay" id="loaderOverlay">
                    <canvas id="dotlottie-canvas" style="width: 250px; height: 250px;"></canvas>
                    <div class="loader-text">Otw Boss...</div>
                </div>

                <div class="header">
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    <button id="logoutBtn" title="Logout" onclick="document.getElementById('logoutForm').submit();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                    <div class="header-main">
                        <img src="https://www.sap.com/dam/application/shared/logos/sap-logo-svg.svg" alt="SAP Logo" class="sap-logo">
                        <h2>Transfer HU</h2>
                    </div>
                    <p>User: <strong>{{ Auth::user()->name ?? 'Guest' }}</strong></p>
                </div>

                @if (session('success'))
                    <div class="notification is-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="notification is-error">
                        {{ session('error') }}
                        @if (session('failed_hus'))
                            <ul class="failed-hu-list">
                                @foreach (session('failed_hus') as $failed)
                                    <li><strong>HU {{ $failed['hu'] }}:</strong> {{ $failed['reason'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <form id="transferForm" action="{{ route('transfer.process') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="huNumber">Nomor HU</label>
                        <div class="input-wrapper">
                            <input type="text" id="huNumber" placeholder="Scan untuk menambahkan HU" readonly>
                            <button type="button" id="startScanBtn" class="btn-icon" title="Scan Barcode">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            </button>
                        </div>
                        <div id="qr-reader" style="display:none;"></div>
                    </div>

                    <div class="hu-list-container">
                        <h3>HU Scanned (<span id="huCounter">0</span>)</h3>
                        <ul id="huList"></ul>
                        <div id="hiddenHuInputs"></div>
                    </div>

                    <div class="form-group">
                        <select id="destSloc" name="destSloc" required>
                            <option value="" disabled selected>Pilih Sloc Tujuan</option>
                            <option value="21HU">21HU_Pack HU FG</option>
                            <option value="224H">224H_SG & Door HU</option>
                            <option value="3DH1">3DH1_HU Store 1</option>
                            <option value="3DH2">3DH2_HU Store 2</option>
                            <option value="3D10">3D10_CGD. Packing</option>
                            <option value="3B02">3B01_Loading 1 SMG</option>
                            <option value="3B02">3B02_Loading 2 SMG HU</option>
                            <option value="3B03">3B03_FG SMG WH A</option>
                            <option value="3B05">3B05_FG SMG WH HU</option>
                            <option value="3B06">3B06_FG Karantina HU</option>
                            <option value="3B07">3B07_HU BOX FG</option>
                            <option value="3G06">3G06_SG SMG Palleting</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Proses Transfer
                        <img src="https://img.icons8.com/external-soft-fill-juicy-fish/60/external-forklift-supermarket-soft-fill-soft-fill-juicy-fish.png" alt="Forklift Icon">
                    </button>
                </form>
            </div>
            <footer class="footer">
                © PT. Kayu Mebel Indonesia, {{ date('Y') }}
            </footer>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script type="module">
        import { DotLottie } from "https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm";

        document.addEventListener('DOMContentLoaded', () => {
            const transferForm = document.getElementById('transferForm');
            const destSlocInput = document.getElementById('destSloc');
            const huListEl = document.getElementById('huList');
            const huCounterEl = document.getElementById('huCounter');
            const loaderOverlay = document.getElementById('loaderOverlay');
            const startScanBtn = document.getElementById('startScanBtn');
            const qrReaderEl = document.getElementById('qr-reader');
            const hiddenHuInputs = document.getElementById('hiddenHuInputs');
            
            let html5QrCode;
            let dotLottie = null; // Inisialisasi sebagai null
            let scannedHUs = [];

            // ## PERUBAHAN 1: Buat fungsi untuk memuat Lottie di awal ##
            async function initializeLottiePlayer() {
                try {
                    dotLottie = new DotLottie({
                        canvas: document.getElementById("dotlottie-canvas"),
                        src: "{{ asset('animations/loading_animation.lottie') }}",
                        loop: true,
                        autoplay: false, // Jangan mainkan otomatis
                    });
                     console.log("Lottie player initialized successfully.");
                } catch (e) {
                    console.error("Failed to initialize Lottie player:", e);
                }
            }

            function updateHiddenInputs() {
                hiddenHuInputs.innerHTML = '';
                scannedHUs.forEach(hu => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'hus[]';
                    input.value = hu;
                    hiddenHuInputs.appendChild(input);
                });
            }

            function renderHuList() {
                huListEl.innerHTML = '';
                scannedHUs.forEach(hu => {
                    const li = document.createElement('li');
                    li.dataset.hu = hu;
                    li.textContent = hu;
                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.className = 'remove-btn';
                    removeBtn.title = `Hapus HU ${hu}`;
                    removeBtn.onclick = () => removeHu(hu);
                    li.appendChild(removeBtn);
                    huListEl.appendChild(li);
                });
                huCounterEl.textContent = scannedHUs.length;
                updateHiddenInputs();
            }

            function addHu(hu) {
                const huNumber = hu.trim();
                if (!huNumber) return;
                if (!scannedHUs.includes(huNumber)) {
                    scannedHUs.push(huNumber);
                    renderHuList();
                }
            }

            function removeHu(huToRemove) {
                scannedHUs = scannedHUs.filter(hu => hu !== huToRemove);
                renderHuList();
            }

            startScanBtn.addEventListener('click', () => {
                const isScannerVisible = qrReaderEl.style.display === 'block';
                if (isScannerVisible) {
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.stop().catch(err => console.error("Gagal stop scanner.", err));
                        qrReaderEl.style.display = 'none';
                    }
                } else {
                    qrReaderEl.style.display = 'block';
                    html5QrCode = new Html5Qrcode("qr-reader");
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    html5QrCode.start({ facingMode: "environment" }, config, addHu)
                        .catch(err => {
                            console.error("Gagal memulai kamera utama:", err);
                            html5QrCode.start({ }, config, addHu)
                                .catch(err2 => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error Kamera',
                                        text: `Gagal mengakses kamera: ${err2}.`,
                                    });
                                });
                        });
                }
            });

            // ## PERUBAHAN 2: Sederhanakan event listener 'submit' ##
            transferForm.addEventListener('submit', function(event) {
                if (scannedHUs.length === 0) {
                    event.preventDefault();
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Belum ada Nomor HU yang di-scan.' });
                    return;
                }

                if (!destSlocInput.value) {
                    event.preventDefault();
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Silakan pilih Sloc Tujuan terlebih dahulu.' });
                    return;
                }

                event.preventDefault();

                loaderOverlay.style.display = 'flex';
                // Cukup mainkan animasi yang sudah dimuat sebelumnya
                if (dotLottie) {
                    dotLottie.play();
                }

                // Tunda submit untuk memberi waktu pada loader untuk tampil
                setTimeout(() => {
                    transferForm.submit();
                }, 100);
            });

            // ## PERUBAHAN 3: Panggil fungsi inisialisasi Lottie saat halaman siap ##
            initializeLottiePlayer();
        });
    </script>
</body>
</html>