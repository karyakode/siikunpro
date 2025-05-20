# 🔐 Siikun Pro – PHP Encoder & Obfuscator

**Siikun Pro** adalah sebuah tools untuk mengamankan kode PHP Anda dengan berbagai fitur proteksi seperti:

- 🔒 Fix Domain
- 🌐 Fix IP
- 📅 Fix Date
- ✅ Tipe Checksum
- 🧩 Obfuscator source code
- 🔐 Encryptor source code
- 🧼 Minify source code

> Siikun Pro dikembangkan oleh [Karya Kode](https://github.com/karyakode)

---

## 📁 Lokasi File

- Akses file `directory.php` untuk melihat daftar hasil enkripsi.
- Hasil enkripsi akan disimpan di dalam folder:  
  ```
  /repository
  ```

---

## 🚀 Cara Install

### 1. Clone dari GitHub
```bash
git clone https://github.com/karyakode/siikunpro.git
cd siikunpro

composer install
```

### 2. Pastikan izin folder benar
```bash
chmod -R 777 system/storage/
chmod -R 777 system/repository/
chmod -R 777 obfuscator_config.json
```

### 3. Akses melalui browser
Jika kamu menggunakan XAMPP/Laragon:
- Pindahkan folder `siikunpro/` ke `htdocs/` atau `www/`
- Akses via:  
  ```
  http://localhost/siikunpro/
  ```

---

## 🧭 Menambahkan Direktori Baru

Untuk menambahkan target direktori enkripsi baru:

1. Jalankan aplikasi di browser.
2. Klik menu **"More Options"**.
3. Masuk ke **"Setting"**.
4. Tambahkan path direktori baru (misalnya: `repository/my-project`).
5. Simpan dan kembali ke halaman utama.

---

## 🛠️ Fitur Utama

| Fitur            | Deskripsi                                      |
|------------------|------------------------------------------------|
| Fix Domain       | Membatasi eksekusi hanya pada domain tertentu |
| Fix IP           | Membatasi berdasarkan alamat IP tertentu      |
| Fix Date         | Membatasi waktu aktif file terenkripsi        |
| Checksum         | Validasi integritas file terenkripsi          |
| Obfuscator       | Mengacak struktur kode agar sulit dibaca      |
| Encryptor        | Mengenkripsi isi source code                  |
| Minifier         | Menghapus whitespace dan komentar             |

---

## 📄 Lisensi

Proyek ini hanya boleh digunakan untuk kebutuhan pribadi atau internal. Beberapa fitur bersifat premium dan hanya tersedia pada versi berbayar. Dilarang menyebarluaskan atau memodifikasi tanpa izin dari pengembang.

---

## 📬 Kontak

Pengembang: **Rohmad Kadarwanto**  
Organisasi: [Karya Kode Indonesia](https://github.com/karyakode)  
Email: `karyakode[at]gmail.com`
