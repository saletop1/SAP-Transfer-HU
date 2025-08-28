const express = require('express');
const path = require('path');
const { Client } = require('node-rfc');
const session = require('express-session');

const app = express();
const port = 3000;

// Koneksi SAP Anda
const sapConnectionParams = {
    ashost: "192.168.254.154",
    sysnr: "01",
    client: "300",
};

// Middleware
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

app.use(session({
    secret: 'kunci-rahasia-untuk-aplikasi-sap-anda-12345', 
    resave: false,
    saveUninitialized: true,
    cookie: { 
        maxAge: 30 * 60 * 1000 
    } 
}));

app.post('/api/login', async (req, res) => {
    const { username, password } = req.body;

    if (!username || !password) {
        return res.status(400).json({ success: false, message: "Username dan password harus diisi." });
    }
    
    const userCredentials = {
        ...sapConnectionParams,
        user: username.toUpperCase(),
        passwd: password
    };

    const client = new Client(userCredentials);

    try {
        console.log("Mencoba login ke SAP dengan user:", username);
        await client.connect();
        await client.close();
        console.log("Login SAP berhasil untuk user:", username);

        req.session.user = {
            username: username.toUpperCase(),
            password: password 
        };
        
        res.json({ success: true, message: "Login berhasil!", user: username });

    } catch (err) {
        console.error("Error saat login SAP:", err);
        res.status(401).json({ success: false, message: err.message || "Kredensial SAP tidak valid." });
    }
});

// ## ENDPOINT BARU: Untuk mendapatkan data user dari sesi ##
app.get('/api/user', (req, res) => {
    if (req.session.user && req.session.user.username) {
        res.json({ success: true, user: req.session.user.username });
    } else {
        res.status(401).json({ success: false, message: 'Tidak terautentikasi' });
    }
});


// =================================================================
// ## Endpoint TRANSFER (Diperbarui untuk memproses array HU) ##
// =================================================================
app.post('/api/transfer', async (req, res) => {
    if (!req.session.user) {
        return res.status(401).json({ success: false, message: "Sesi tidak valid." });
    }

    // ## PERBAIKAN: Menerima array 'hus' dari body request ##
    const { hus, destSloc } = req.body; 

    if (!hus || !Array.isArray(hus) || hus.length === 0) {
        return res.status(400).json({ success: false, message: "Daftar HU tidak boleh kosong." });
    }
    
    const { username, password } = req.session.user;
    
    const rfcUserCredentials = {
        ...sapConnectionParams,
        user: username,
        passwd: password
    };
    
    const client = new Client(rfcUserCredentials);
    
    try {
        await client.connect();

        let successCount = 0;
        let failedHUs = [];

        // ## PERBAIKAN: Melakukan looping untuk setiap HU dalam array ##
        for (const hu of hus) {
            const paddedHu = hu.padStart(20, '0');
            console.log(`User '${username}' memproses HU: ${paddedHu}`);
            
            const rfcParameters = {
                IV_HU_NUMBER: paddedHu,
                IV_LGORT: destSloc,
            };

            const result = await client.call("ZRFC_HU_GOODS_MOVEMENT", rfcParameters);
            
            // Cek error untuk HU ini
            const hasError = result.ET_RETURN && result.ET_RETURN.some(msg => msg.TYPE === 'E' || msg.TYPE === 'A');
            
            if (hasError) {
                 const errorMsg = result.ET_RETURN.find(msg => msg.TYPE === 'E' || msg.TYPE === 'A').MESSAGE;
                 failedHUs.push({ hu: hu, reason: errorMsg });
            } else {
                successCount++;
            }
        }

        await client.close();
        
        if (failedHUs.length > 0) {
            throw new Error(`Berhasil: ${successCount}, Gagal: ${failedHUs.length}. Error pertama pada HU ${failedHUs[0].hu}: ${failedHUs[0].reason}`);
        }

        res.json({ success: true, message: `${successCount} HU berhasil ditransfer.` });

    } catch (err) {
        console.error("Error saat transfer HU:", err);
        res.status(500).json({ success: false, message: err.message });
    }
});


app.post('/api/logout', (req, res) => {
    req.session.destroy(err => {
        if (err) {
            return res.status(500).json({ success: false, message: "Gagal untuk logout." });
        }
        res.clearCookie('connect.sid');
        res.json({ success: true, message: "Anda berhasil logout." });
    });
});

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.listen(port, () => {
    console.log(`Server berjalan di http://localhost:${port}`);
});
