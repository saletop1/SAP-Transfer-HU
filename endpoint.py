import os
from datetime import timedelta
from flask import Flask, request, jsonify
from flask_session import Session
from pyrfc import Connection, RFCError
from dotenv import load_dotenv
from flask_cors import CORS

# =================================================================
# 1. KONFIGURASI DAN INISIALISASI
# =================================================================

load_dotenv()
app = Flask(__name__)
CORS(app)

app.config["SECRET_KEY"] = os.urandom(24)
app.config["SESSION_TYPE"] = "filesystem"
app.config["SESSION_PERMANENT"] = True
app.config["PERMANENT_SESSION_LIFETIME"] = timedelta(minutes=30)
Session(app)

sap_connection_params = {
    "ashost": os.getenv("SAP_HOST", "192.168.254.154"),
    "sysnr": os.getenv("SAP_SYSNR", "01"),
    "client": os.getenv("SAP_CLIENT", "300"),
    "lang": os.getenv("SAP_LANG", "EN")
}

# =================================================================
# 2. FUNGSI BANTUAN (HELPER FUNCTIONS)
# =================================================================

# ## TAMBAHAN 1: Fungsi untuk mengambil kredensial dari header ##
# Meniru pola dari contoh api.py yang sudah berhasil.
def get_credentials():
    """Mengambil kredensial SAP dari header request."""
    username = request.headers.get('X-SAP-Username')
    password = request.headers.get('X-SAP-Password')
    
    if not username or not password:
        raise ValueError("SAP credentials (X-SAP-Username, X-SAP-Password) not found in headers.")
    
    return username, password

# ## TAMBAHAN 2: Fungsi untuk membuat koneksi SAP ##
# Sentralisasi logika pembuatan koneksi agar tidak berulang.
def connect_sap(username, password):
    """Membuat objek koneksi SAP dengan kredensial yang diberikan."""
    creds = {
        **sap_connection_params,
        "user": username.upper(),
        "passwd": password
    }
    return Connection(**creds)

# =================================================================
# 3. LOGIKA BISNIS (TIDAK DIUBAH)
# =================================================================

def sap_login_check(username, password):
    """Mencoba login ke SAP untuk memvalidasi kredensial."""
    conn = None
    try:
        conn = connect_sap(username, password)
        conn.ping()
        app.logger.info(f"Login SAP berhasil untuk user: {username}")
        return {"success": True, "message": "Login berhasil!"}
    except RFCError as e:
        app.logger.error(f"Error saat login SAP untuk user {username}: {e}")
        return {"success": False, "message": str(e)}
    finally:
        if conn:
            conn.close()

def sap_transfer_hus(user_creds_dict, hus, dest_sloc):
    """Memproses transfer untuk daftar Handling Units (HU)."""
    conn = None
    try:
        # Fungsi ini tetap menerima dictionary kredensial lengkap
        conn = Connection(**user_creds_dict)
        
        success_count = 0
        failed_hus = []
        success_hus = []

        for hu in hus:
            padded_hu = hu.zfill(20)
            app.logger.info(f"User '{user_creds_dict['user']}' memproses HU: {padded_hu} ke SLoc: {dest_sloc}")

            result = conn.call(
                "ZRFC_HU_GOODS_MOVEMENT",
                IV_HU_NUMBER=padded_hu,
                IV_LGORT=dest_sloc
            )
            
            has_error = any(msg['TYPE'] in ('E', 'A') for msg in result.get('ET_RETURN', []))

            if has_error:
                error_msg = next((msg['MESSAGE'] for msg in result['ET_RETURN'] if msg['TYPE'] in ('E', 'A')), "Error tidak diketahui.")
                failed_hus.append({"hu": hu, "reason": error_msg})
            else:
                success_count += 1
                success_hus.append(hu)
        
        if failed_hus:
            return {
                "success": False,
                "message": f"Transfer selesai dengan {len(failed_hus)} kegagalan.",
                "details": {
                    "success_count": success_count,
                    "failed_count": len(failed_hus),
                    "successful_hus": success_hus,
                    "failed_hus": failed_hus
                }
            }

        return {"success": True, "message": f"Semua {success_count} HU berhasil ditransfer."}
    
    except RFCError as e:
        app.logger.error(f"RFC Error saat transfer HU: {e}")
        raise e
    finally:
        if conn:
            conn.close()

# =================================================================
# 4. ENDPOINTS (ROUTES)
# =================================================================

@app.route('/api/login', methods=['POST'])
def login():
    """Endpoint ini tetap sama, untuk validasi awal kredensial dari body."""
    data = request.get_json()
    if not data:
        return jsonify({"success": False, "message": "Request body JSON tidak valid."}), 400

    username = data.get('username')
    password = data.get('password')

    if not username or not password:
        return jsonify({"success": False, "message": "Username dan password harus diisi."}), 400

    result = sap_login_check(username, password)

    if result["success"]:
        return jsonify({"success": True, "message": "Login berhasil!", "user": username.upper()})
    else:
        return jsonify({"success": False, "message": result["message"]}), 401

# ## PERUBAHAN UTAMA DI SINI ##
@app.route('/api/transfer', methods=['POST'])
def transfer():
    try:
        # Langkah 1: Ambil kredensial dari header, bukan body
        username, password = get_credentials()

        # Langkah 2: Ambil data proses dari body JSON
        data = request.get_json()
        if not data:
            return jsonify({"success": False, "message": "Request body tidak valid."}), 400

        hus = data.get('hus')
        dest_sloc = data.get('destSloc')

        if not hus or not dest_sloc:
            return jsonify({"success": False, "message": "Parameter hus dan destSloc wajib diisi."}), 400
        
        if not isinstance(hus, list) or not hus:
            return jsonify({"success": False, "message": "Daftar HU harus berupa list dan tidak boleh kosong."}), 400

        # Langkah 3: Siapkan dictionary kredensial untuk fungsi bisnis
        user_creds = {
            **sap_connection_params,
            "user": username.upper(),
            "passwd": password
        }

        # Langkah 4: Panggil logika bisnis (tidak ada perubahan di sini)
        result = sap_transfer_hus(user_creds, hus, dest_sloc)
        
        if not result.get("success"):
            return jsonify(result), 422 # Unprocessable Entity
        
        return jsonify(result)
        
    except ValueError as ve:
        # Menangkap error jika header kredensial tidak ada
        return jsonify({"success": False, "message": str(ve)}), 401 # Unauthorized
    except Exception as e:
        # Menangkap error internal lainnya
        app.logger.error(f"Internal error on transfer: {e}")
        return jsonify({"success": False, "message": f"Terjadi error internal: {e}"}), 500

# =================================================================
# 5. MENJALANKAN SERVER
# =================================================================

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=8080, debug=True)