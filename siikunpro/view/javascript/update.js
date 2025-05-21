const endpoint = {
    status: 'index.php?route=/api/update/getStatus',
    check: 'index.php?route=/api/update/checkVersion',
    update: 'index.php?route=/api/update/processUpdate'
};

function updateStatusUI(data) {
    document.getElementById('update-status').textContent = data.status_label;
    document.getElementById('current-version').textContent = data.current_version;
    document.getElementById('latest-version').textContent = data.latest_version;
    //document.getElementById('update-log').value = renderRelease(data.log[0]) || '';
    //tampilkanLog(data.log,document.getElementById('update-log'));
    
}


function formatSize(bytes) {
    return (bytes / 1024 / 1024).toFixed(2) + ' MB';
}

function formatDate(iso) {
    const date = new Date(iso);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function renderRelease(release) {
    const container = document.getElementById('release-info');
    const assetList = release.assets.map(asset => `
      <li>
        <a href="${asset.browser_download_url}" target="_blank">${asset.name}</a>
        (${formatSize(asset.size)})
      </li>
    `).join('');

    container.innerHTML = `
      <h2>🚀 Rilis Baru: ${release.name}</h2>
      <p><strong>Tag:</strong> ${release.tag_name}</p>
      <p><strong>Tanggal Rilis:</strong> ${formatDate(release.published_at)}</p>
      <p><strong>Changelog:</strong><br>${release.body.replace(/\n/g, "<br>")}</p>
      <h3>📦 Assets:</h3>
      <ul>${assetList}</ul>
      <p><a href="${release.html_url}" target="_blank">🔗 Lihat di GitHub</a></p>
    `;
}



function tampilkanLog(logText, textarea) {
    if (!textarea) return;

    // Jika logText bukan string, ubah ke string
    if (typeof logText !== 'string') {
        if (Array.isArray(logText)) {
            logText = logText.join('\n');
        } else {
            logText = String(logText);
        }
    }

    const lines = logText.split('\n');
    textarea.value = ''; // reset textarea

    lines.forEach(line => {
        const firstCommaIndex = line.indexOf(',');
        if (firstCommaIndex === -1) {
            textarea.value += line + '\n';
            return;
        }

        const timestamp = line.substring(0, firstCommaIndex).trim();
        const logContent = line.substring(firstCommaIndex + 1).trim();
        

        // Jika mengandung JSON setelah teks tertentu
        //if (logContent.startsWith('Pre-release found:')) {
            const jsonStart = logContent.indexOf('{');
            if (jsonStart !== -1) {
                const jsonString = logContent.substring(jsonStart);
                try {
                    const json = JSON.parse(jsonString);
                    textarea.value += `${timestamp}\n`;
                    textarea.value += 'Pre-release ditemukan:\n';
                    textarea.value += JSON.stringify(json, null, 2) + '\n\n';
                } catch (e) {
                    textarea.value += `${timestamp} ❌ Gagal parse JSON\n${logContent}\n\n`;
                }
                return;
            }
        //}

        try {
            const json = JSON.parse(logContent);
            textarea.value += `${timestamp}\n`;
            textarea.value += JSON.stringify(json, null, 2) + '\n\n';
        } catch (e) {
            //textarea.value += `${timestamp} - ${logContent}\n`;
        }
    });

    textarea.scrollTop = textarea.scrollHeight;
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

// Fungsi async eksternal
async function fetchVersion() {
    try {
        const response = await fetch(endpoint.check);
        const res = await response.json();

        if (res.status === 'success') {
            document.getElementById('update-status').textContent = res.message;
            document.getElementById('current-version').textContent = res.data.current_version;
            document.getElementById('latest-version').textContent = res.data.latest_version;
        } else {
            alert('Gagal mengecek versi: ' + res.message);
        }
    } catch (err) {
        console.error('Gagal mengecek versi:', err);
        alert('Terjadi kesalahan saat mengecek versi.');
    }
}

async function runUpdate() {
    if (!confirm('Yakin ingin menjalankan update?')) return;

    const statusEl = document.getElementById('update-status');
    const logEl = document.getElementById('update-log');
    const btnUpdate = document.getElementById('btn-update');
    
    if (statusEl) statusEl.textContent = 'Memulai update...';
    //if (logEl) logEl.value += 'Memulai update...\n';

    try {
        const response = await fetch(endpoint.update);
        const res = await response.json();

        if (res.status === 'success') {
            //if (statusEl) statusEl.textContent = 'Update berhasil.';
            if (logEl) {
                //logEl.value += 'Update berhasil dijalankan.\n';
                //logEl.value += JSON.stringify(res.data, null, 2) + '\n';
            }
            //updateStatusUI(res.data);
        } else {
            alert('Update gagal: ' + res.message);
            if (statusEl) statusEl.textContent = 'Update gagal.';
            //if (logEl) logEl.value += `Update gagal: ${res.message}\n`;
        }

        await fetchStatus(); // polling ulang status

    } catch (err) {
        console.error('Gagal menjalankan update:', err);
        alert('Terjadi kesalahan saat update.');

        if (statusEl) statusEl.textContent = 'Terjadi kesalahan.';
        //if (logEl) logEl.value += `Kesalahan: ${err.message}\n`;
    } finally {
        if (btnUpdate) {
            btnUpdate.disabled = false;
            btnUpdate.innerHTML = `<i class="fas fa-download"></i> Jalankan Update`;
        }
    }
}

const modalUpdaterContent = async (body) => {
    if (!body) {
        console.warn('Elemen body tidak ditemukan');
        return;
    }

    // Reset konten body
    body.innerHTML = '';
    const updaterContent = createElement('div', '', '', { id: 'updaterContent' });
    body.appendChild(updaterContent);

    // Spinner loading utama
    const loadingIndicator = createElement('div', 'text-center', `
        <div class="spinner-border text-primary" role="status"></div>
        <div>Mohon tunggu, sedang memuat informasi versi...</div>
    `);
    updaterContent.appendChild(loadingIndicator);

    // Elemen status
    const statusBox = createElement('div', 'mb-3', '', { id: 'status-box' });
    statusBox.innerHTML = `
        <p><strong>Status:</strong> <span id="update-status" class="text-info">Memuat...</span></p>
        <p><strong>Versi Saat Ini:</strong> <span id="current-version">-</span></p>
        <p><strong>Versi Terbaru:</strong> <span id="latest-version">-</span></p>
    `;

    // Tombol update
    const buttonGroup = createElement('div', 'btn-group mb-3');
    const btnUpdate = createElement('button', 'btn btn-primary btn-block mt-2 mb-2', `
        <i class="fas fa-download"></i> Jalankan Update
    `, {
        id: 'btn-update',
        type: 'button'
    });

    // Area log
    const formGroup = createElement('div', 'form-group');
    const label = createElement('label', '', 'Log Update:', { for: 'update-log' });
    const textarea = createElement('textarea', 'form-control', '', {
        id: 'update-log',
        rows: 10,
        readOnly: true
    });

    formGroup.appendChild(label);
    formGroup.appendChild(textarea);

    try {
        const response = await fetch(endpoint.check);
        const res = await response.json();

        loadingIndicator.remove();
        updaterContent.appendChild(statusBox);

        if (res.status === 'success') {
            document.getElementById('update-status').textContent = res.message;
            document.getElementById('current-version').textContent = res.data.current_version;
            document.getElementById('latest-version').textContent = res.data.latest_version || '-';

            if (!res.data.is_latest) {
                buttonGroup.appendChild(btnUpdate);
                updaterContent.appendChild(buttonGroup);

                btnUpdate.addEventListener('click', async () => {
                    btnUpdate.disabled = true;
                    btnUpdate.innerHTML = `
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Sedang Mengupdate...
                    `;
                    await runUpdate(); // Pastikan runUpdate mendukung async
                    // Setelah selesai, kamu bisa mengaktifkan kembali tombol atau menampilkan info selesai
                });
            } else {
                const info = createElement('div', 'alert alert-success text-center', 'Aplikasi sudah menggunakan versi terbaru.');
                updaterContent.appendChild(info);
            }

            //updaterContent.appendChild(formGroup);

            // Kondisi khusus reload konten
            if (!res.data.is_latest && !res.data.latest_version) {
                await modalUpdaterContent(body);
            }

        } else {
            const errorMsg = createElement('div', 'alert alert-warning text-center', `Gagal mengecek versi: ${res.message}`);
            updaterContent.appendChild(errorMsg);
        }

    } catch (err) {
        console.error('Gagal mengecek versi:', err);
        loadingIndicator.remove();

        const errorMsg = createElement('div', 'alert alert-danger text-center', 'Terjadi kesalahan saat mengecek versi.');
        updaterContent.appendChild(errorMsg);

        const retryBtn = createElement('button', 'btn btn-warning mt-2', 'Coba Lagi');
        retryBtn.addEventListener('click', () => modalUpdaterContent(body));
        updaterContent.appendChild(retryBtn);
    }
};


// Konfigurasi modal settings
const modalUpdateApp = {
    id: 'modalUpdateApp',
    title: 'Updater v1.0',
    loading: true,
    loadingText: 'Mohon tunggu, sedang memuat informasi versi...',
    onLoad: modalUpdaterContent,
    //footer: false
};


// Event listeners
document.addEventListener('DOMContentLoaded', function () {
    //fetchVersion();

    //document.getElementById('btn-check-version').addEventListener('click', fetchVersion);
    //document.getElementById('btn-update').addEventListener('click', runUpdate);
});