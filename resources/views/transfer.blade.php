{{-- resources/views/transfer.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transfer HU antar Sloc</title>
    
    {{-- (Bagian CSS dan link font tetap sama, tidak saya tampilkan untuk keringkasan) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm"></script>
    <style>
        :root {
            --primary-color: #116ed1; 
            --secondary-color: #6c757d; 
            --success-color: #198754;
            --error-color: #dc3545; 
            --light-gray: #f4f7f6; 
            --dark-gray: #116ed1;
            --text-black: #333;
            --border-radius: 8px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--dark-gray);
            display: flex; flex-direction: column; min-height: 100vh; }
        
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .card { background-color: white; padding: 2rem; border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); width: 100%; max-width: 450px;
            position: relative;
            overflow: hidden;
        }
        .header { text-align: center; margin-bottom: 2rem; position: relative; }
        .header-main {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .sap-logo {
            height: 28px;
        }
        .header h2 { 
            font-weight: 600; 
            margin: 0;
            font-size: 1.75rem;
        }
        .header p { color: var(--secondary-color); }
        .form-group { margin-bottom: 1.5rem; }
        
        label, .hu-list-container h3 { 
            display: block; 
            font-weight: 500; 
            margin-bottom: 0.5rem;
            font-size: 1rem; 
        }

        .input-wrapper { display: flex; align-items: center; gap: 0.75rem; }
        .input-wrapper input { flex-grow: 1; }
        .btn-icon { flex-shrink: 0; width: 48px; height: 48px; display: flex; justify-content: center; align-items: center;
            background-color: white; border: 1px solid #ddd; border-radius: var(--border-radius); cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s; }
        .btn-icon:hover { background-color: var(--light-gray); border-color: #ccc; }
        .btn-icon svg { width: 24px; height: 24px; color: var(--secondary-color); }
        
        input[type="text"], select {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: var(--border-radius);
            font-size: 1rem; transition: border-color 0.3s, box-shadow 0.3s; background-color: white;
            color: var(--dark-gray);
        }
        input[readonly] { background-color: #f8f9fa; cursor: default; }
        
        select {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236c757d%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat; background-position: right .9em top 50%; background-size: .65em auto;
            color: var(--secondary-color);
        }
        select:valid {
            color: var(--text-black);
        }

        input[type="text"]:focus, select:focus {
            outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(17, 110, 209, 0.15);
        }
        .btn { display: flex; justify-content: center; align-items: center; gap: 0.75rem; width: 100%; padding: 12px;
            border: none; border-radius: var(--border-radius); font-size: 1rem; font-weight: 500; cursor: pointer;
            transition: background-color 0.3s, transform 0.1s; }
        .btn-primary { background-color: var(--primary-color); color: white; margin-top: 1rem; }
        .btn-primary:hover { background-color: #0d5ab8; }
        .btn-primary img { width: 32px; height: 32px; }
        #logoutBtn { position: absolute; top: -10px; right: -10px; background: none; border: none; cursor: pointer; color: var(--secondary-color); }
        #qr-reader { width: 100%; border-radius: var(--border-radius); overflow: hidden; margin-top: 1rem; }
        #qr-reader.scanner-error svg path { stroke: var(--error-color) !important; }
        #message { margin-top: 1.5rem; padding: 1rem; border-radius: var(--border-radius); text-align: center; font-weight: 500; display: none; }
        .success { display: block; background-color: #e9f7ef; color: var(--success-color); }
        .error { display: block; background-color: #fceeee; color: var(--error-color); }
        
        .loader-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.9);
            display: none; flex-direction: column; justify-content: center; align-items: center; z-index: 10; gap: 1rem; }
        .loader-text { font-weight: 500; color: var(--dark-gray); }

        .hu-list-container { margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1.5rem; }
        .hu-list-container h3 { margin-bottom: 1rem; text-align: left; }
        #huList { list-style: none; max-height: 150px; overflow-y: auto; border: 1px solid #ddd; border-radius: var(--border-radius); padding: 0.5rem; }
        #huList li { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid #eee; transition: color 0.3s, font-weight 0.3s; color: var(--text-black); }
        #huList li:last-child { border-bottom: none; }
        .remove-btn { background: none; border: none; color: var(--error-color); cursor: pointer; font-weight: bold; font-size: 1.2rem; }
        
        .input-error { animation: shake 0.8s; border-color: var(--error-color) !important; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        
        .hu-duplicate { color: var(--error-color); font-weight: 600; }
        .hu-success {
            color: var(--success-color);
            font-weight: 600;
        }
        .footer {
            text-align: center;
            padding: 1.5rem;
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <main>
        <div class="card">
            {{-- (Bagian loader, header, dan form input HU tetap sama) --}}
            <div class="loader-overlay" id="loaderOverlay">
                <canvas id="dotlottie-canvas" style="width: 250px; height: 250px;"></canvas>
                <div class="loader-text">Otw Boss...</div>
            </div>
            <div class="header">
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                <button id="logoutBtn" title="Logout" onclick="document.getElementById('logoutForm').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
                <div class="header-main">
                    <img src="https://www.sap.com/dam/application/shared/logos/sap-logo-svg.svg" alt="SAP Logo" class="sap-logo">
                    <h2>Transfer HU</h2>
                </div>
                <p>User: <strong>{{ Auth::user()->name ?? 'Guest' }}</strong></p>
            </div>
            
            <form id="transferForm" action="{{ route('transfer.process') }}" method="POST">
                <div class="form-group">
                    <label for="huNumber">Nomor HU</label>
                    <div class="input-wrapper">
                        <input type="text" id="huNumber" placeholder="Scan untuk menambahkan HU" readonly>
                        <button type="button" id="startScanBtn" class="btn-icon" title="Scan Barcode">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        </button>
                    </div>
                    <div id="qr-reader" style="display:none;"></div>
                </div>
                
                <div class="hu-list-container">
                    <h3>HU Scanned (<span id="huCounter">0</span>)</h3>
                    <ul id="huList"></ul>
                </div>
                
                <div class="form-group">
                    {{-- ## PERUBAHAN: Opsi Sloc dikembalikan menjadi hardcode ## --}}
                    <select id="destSloc" required>
                        <option value="" disabled selected>Pilih Sloc Tujuan</option>
                        <option value="3DH1">3DH1</option>
                        <option value="3DH2">3DH2</option>
                        <option value="3D10">3D10</option>
                        <option value="3B02">3B02</option>
                        <option value="3B03">3B03</option>
                        <option value="3B05">3B05</option>
                        <option value="3B06">3B06</option>
                        <option value="3B07">3B07</option>
                        <option value="3G06">3G06</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Proses Transfer
                    <img src="https://img.icons8.com/external-soft-fill-juicy-fish/60/external-forklift-supermarket-soft-fill-soft-fill-juicy-fish.png" alt="Forklift Icon">
                </button>
            </form>

            <div id="message"></div>
        </div>
    </main>

    <footer class="footer">
        © PT. Kayu Mebel Indonesia, {{ date('Y') }}
    </footer>
    {{-- QR Code Scanner library from CDN --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script type="module">
        import { DotLottie } from "https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm";

        window.sapCredentials = {
            username: "{{ session('sap_username', '') }}",
            password: "{{ session('sap_password', '') }}"
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            // ## PERBAIKAN: Definisikan URL dan token CSRF di awal ##
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const LOGIN_URL = "{{ route('login') }}";

            // Fungsi helper untuk menangani respons API, terutama untuk session expired (401)
            async function handleApiResponse(response) {
                if (response.status === 401) {
                    alert("Sesi Anda telah berakhir. Harap login kembali.");
                    window.location.href = LOGIN_URL;
                    return null;
                }
                return response.json();
            }
            
            // Deklarasi elemen-elemen DOM
            const huInput = document.getElementById('huNumber');
            const destSlocInput = document.getElementById('destSloc');
            const transferForm = document.getElementById('transferForm');
            const huListEl = document.getElementById('huList');
            const huCounterEl = document.getElementById('huCounter');
            const messageEl = document.getElementById('message');
            const loaderOverlay = document.getElementById('loaderOverlay');
            const startScanBtn = document.getElementById('startScanBtn');
            const qrReaderEl = document.getElementById('qr-reader');
            let html5QrCode;
            let dotLottie;
            
            let scannedHUs = []; // State untuk menyimpan HU yang sudah di-scan

            // Fungsi untuk me-render daftar HU ke dalam <ul>
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
            }

            // Fungsi untuk menambahkan HU baru
            function addHu(hu) {
                const huNumber = hu.trim();
                if (!huNumber) return;

                if (scannedHUs.includes(huNumber)) {
                    // Beri feedback visual jika HU sudah ada (duplikat)
                    const existingLi = huListEl.querySelector(`li[data-hu="${huNumber}"]`);
                    if (existingLi) {
                        existingLi.classList.add('hu-duplicate');
                        setTimeout(() => existingLi.classList.remove('hu-duplicate'), 1000);
                    }
                    if (qrReaderEl.style.display === 'block') {
                        qrReaderEl.classList.add('scanner-error');
                        setTimeout(() => qrReaderEl.classList.remove('scanner-error'), 1000);
                    }
                } else {
                    // Tambahkan HU baru ke dalam array dan render ulang
                    scannedHUs.push(huNumber);
                    renderHuList();
                    const newLi = huListEl.querySelector(`li[data-hu="${huNumber}"]`);
                    if (newLi) {
                        newLi.classList.add('hu-success');
                        setTimeout(() => newLi.classList.remove('hu-success'), 1500);
                    }
                }
                huInput.value = '';
            }

            // Fungsi untuk menghapus HU dari daftar
            function removeHu(huToRemove) {
                scannedHUs = scannedHUs.filter(hu => hu !== huToRemove);
                renderHuList();
            }

            // Event listener untuk tombol Scan
            startScanBtn.addEventListener('click', () => {
                const isScannerVisible = qrReaderEl.style.display === 'block';
                if (isScannerVisible) {
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.stop().then(() => qrReaderEl.style.display = 'none').catch(err => console.error("Gagal stop scanner.", err));
                    }
                } else {
                    qrReaderEl.style.display = 'block';
                    html5QrCode = new Html5Qrcode("qr-reader");
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    
                    html5QrCode.start({ facingMode: "environment" }, config, addHu)
                        .catch(err => {
                            messageEl.textContent = `Error Kamera: ${err}. Pastikan izin kamera sudah diberikan.`;
                            messageEl.className = 'error';
                            messageEl.style.display = 'block';
                            qrReaderEl.style.display = 'none';
                        });
                }
            });

            // Event listener untuk submit form transfer
            transferForm.addEventListener('submit', async function(event){
                event.preventDefault();
                if (scannedHUs.length === 0) {
                    messageEl.textContent = 'Error: Belum ada Nomor HU yang di-scan.';
                    messageEl.className = 'error';
                    return;
                }

                messageEl.style.display = 'none';
                loaderOverlay.style.display = 'flex';
                
                // ... (Logika Lottie tetap sama) ...
                if (!dotLottie) {
                    dotLottie = new DotLottie({ canvas: document.getElementById("dotlottie-canvas"), src: "https://lottie.host/79b7d6c7-1218-429d-9ff3-4ebf563e1c95/FCoH1GLOQc.lottie", loop: true, autoplay: true });
                } else {
                    dotLottie.play();
                }
                
                // ## PERBAIKAN 1: Payload body sekarang HANYA berisi data proses ##
                const data = { 
                    hus: scannedHUs, 
                    destSloc: destSlocInput.value,
                };

                console.log("Mengirim data:", data);
                console.log("Menggunakan kredensial di header:", window.sapCredentials.username);

                try {
                    const response = await fetch('http://127.0.0.1:8080/api/transfer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            // ## PERBAIKAN 2: Kredensial dipindahkan ke header ##
                            'X-SAP-Username': window.sapCredentials.username,
                            'X-SAP-Password': window.sapCredentials.password
                        },
                        body: JSON.stringify(data) // Body sekarang tidak berisi kredensial
                    });

                    const result = await handleApiResponse(response);
                    if (!result) return;

                    if(response.ok) {
                        messageEl.textContent = `Sukses: ${result.message}`;
                        messageEl.className = 'success';
                        scannedHUs = [];
                        renderHuList();
                        destSlocInput.selectedIndex = 0;
                    } else {
                        let errorMessage = `Error: ${result.message}`;
                        if (result.details && result.details.failed_hus && result.details.failed_hus.length > 0) {
                            const failedItems = result.details.failed_hus.map(item => `\n- HU ${item.hu}: ${item.reason}`).join('');
                            errorMessage += `\nDetail Kegagalan:${failedItems}`;
                        }
                        messageEl.textContent = errorMessage;
                        messageEl.style.whiteSpace = 'pre-wrap';
                        messageEl.className = 'error';
                    }
                } catch(error) {
                    messageEl.textContent = 'Error: Gagal terhubung ke server Flask. Pastikan API berjalan.';
                    messageEl.className = 'error';
                } finally {
                    messageEl.style.display = 'block';
                    loaderOverlay.style.display = 'none';
                    if (dotLottie) dotLottie.stop();
                }
            });
        });
    </script>
</body>
</html>