<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Update System - Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- AdminLTE 3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">

  <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <!-- AdminLTE -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <div class="content-wrapper p-4">
    <div class="container">
      <h3><i class="fas fa-sync-alt"></i> Sistem Pembaruan</h3>

      <div class="card mt-3">
        <div class="card-body">
          <div id="status-box" class="mb-3">
            <p><strong>Status:</strong> <span id="update-status" class="text-info">Memuat...</span></p>
            <p><strong>Versi Saat Ini:</strong> <span id="current-version">-</span></p>
            <p><strong>Versi Terbaru:</strong> <span id="latest-version">-</span></p>
          </div>

          <div class="btn-group mb-3">
            <button class="btn btn-info" id="btn-check-version"><i class="fas fa-search"></i> Cek Versi</button>
            <button class="btn btn-success" id="btn-update"><i class="fas fa-download"></i> Jalankan Update</button>
          </div>

          <div class="form-group">
            <label for="update-log">Log Update:</label>
            <textarea id="update-log" class="form-control" rows="10" readonly></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const endpoint = {
  status: 'index.php?route=/api/update/getStatus',
  check: 'index.php?route=/api/update/checkVersion',
  update: 'index.php?route=/api/update/processUpdate'
};

function updateStatusUI(data) {
  document.getElementById('update-status').textContent = data.status_label;
  document.getElementById('current-version').textContent = data.current_version;
  document.getElementById('latest-version').textContent = data.latest_version;
  document.getElementById('update-log').value = data.log || '';
}

function fetchStatus() {
  fetch(endpoint.status)
    .then(response => response.json())
    .then(res => {
      if (res.status === 'success') {
        updateStatusUI(res.data);

        // Jika masih updating, terus polling
        if (res.data.status_label === 'updating') {
          setTimeout(fetchStatus, 2000);
        }
      } else {
        document.getElementById('update-status').textContent = 'Gagal memuat status';
      }
    })
    .catch(err => {
      console.error('Gagal mengambil status:', err);
      document.getElementById('update-status').textContent = 'Gagal memuat status';
    });
}

function fetchVersion() {
  fetch(endpoint.check)
    .then(response => response.json())
    .then(res => {
      if (res.status === 'success') {
        document.getElementById('update-status').textContent = res.message;
        document.getElementById('current-version').textContent = res.data.current_version;
        document.getElementById('latest-version').textContent = res.data.latest_version;
      } else {
        alert('Gagal mengecek versi: ' + res.message);
      }
    })
    .catch(err => {
      console.error('Gagal mengecek versi:', err);
      alert('Terjadi kesalahan saat mengecek versi.');
    });
}

function runUpdate() {
  if (!confirm('Yakin ingin menjalankan update?')) return;

  document.getElementById('update-status').textContent = 'Memulai update...';

  fetch(endpoint.update)
    .then(response => response.json())
    .then(res => {
      if (res.status === 'success') {
        updateStatusUI(res.data);
        alert(res.message);
      } else {
        alert('Update gagal: ' + res.message);
      }

      fetchStatus(); // polling ulang
    })
    .catch(err => {
      console.error('Gagal menjalankan update:', err);
      alert('Terjadi kesalahan saat update.');
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function () {
  fetchVersion();

  document.getElementById('btn-check-version').addEventListener('click', fetchVersion);
  document.getElementById('btn-update').addEventListener('click', runUpdate);
});
</script>
</body>
</html>
