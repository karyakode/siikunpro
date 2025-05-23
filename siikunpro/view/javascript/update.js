const endpoint = {
    status: 'index.php?route=/api/update/checkStatus',
    check: 'index.php?route=/api/update/getStatus',
    update: 'index.php?route=/api/update/processUpdate',
    downloadProgress: 'index.php?route=/api/update/downloadProgress'
};

function updateStatusUI(data) {
    document.getElementById('update-status').textContent = data.status_label ?? data.message;
    document.getElementById('current-version').textContent = data.current_version ?? '-';
    document.getElementById('latest-version').textContent = data.latest_version ?? '-';
    document.getElementById('size-mb').textContent = data.size_mb ?? '-';
    document.getElementById('pre-release').textContent = data.pre_release ?? '-';
    
}

const INIT = 100;
const UPDATED = 200;
const LATEST = 204;
const ERROR = 500;
const BUSY = 504;

async function fetchStatus() {
    const btnUpdate = document.getElementById('btn-update');

    try {
        const response = await fetch(endpoint.status);
        const res = await response.json();

        if (res.status === 'success') {
            // Jika masih updating, polling ulang
            if (res.data.status_label === 'updating') {
                timeoutManager.startTimeout('updatingSiikun', async () => {
                    await fetchStatus(); // <-- perlu dipanggil sebagai fungsi
                }, 2); // delay dalam detik?
            } else if (res.data.status_label === 'updated'){
                if (btnUpdate) {
                    btnUpdate.disabled = false;
                    btnUpdate.innerHTML = `<i class="fas fa-download"></i> Jalankan Update`;
                }

                updateStatusUI(res.data);

            }
        } else {
            document.getElementById('update-status').textContent = 'Gagal memuat status';
        }
    } catch (error) {
        ConsoleManager.error('Gagal mengambil status:', error);
        document.getElementById('update-status').textContent = 'Gagal memuat status';
    } finally {
        
    }
}

function clearAppCache() {
    // 1. Hapus Cache API (Service Worker cache)
    if ('caches' in window) {
        caches.keys().then(function (names) {
            for (let name of names) {
                caches.delete(name);
            }
        });
    }

    // 2. Hapus LocalStorage & SessionStorage
    localStorage.clear();
    sessionStorage.clear();

    // 3. Hapus Cookies
    document.cookie.split(";").forEach(function (cookie) {
        document.cookie = cookie
            .replace(/^ +/, "")
            .replace(/=.*/, "=;expires=" + new Date(0).toUTCString() + ";path=/");
    });

    // 4. Optional: Cegah user kembali ke halaman sebelumnya
    history.pushState(null, null, location.href);
    window.addEventListener("popstate", function () {
        history.pushState(null, null, location.href);
    });

    // 5. Optional: Reload halaman tanpa cache
    window.location.href = window.location.href.split('?')[0] + '?clear=' + new Date().getTime();
};


function pollDownloadProgress() {
    const infoElement = document.getElementById('download-info');
    const btnUpdate = document.getElementById('btn-update');
    const progressBarUpdater = document.querySelector('.progress-bar-update');
    const fileSizeText = document.querySelector('.file-size-update');
    const progressPercentageText = document.querySelector('.progress-percentage-update');
    const updateStatus = document.getElementById('update-status');

    timeoutManager.startTimeout('downloadPolling', async () => {
        try {
            const response = await fetch(endpoint.downloadProgress);
            const result = await response.json();

            if (result.status === 'success') {
                const { size_mb, size, size_target_mb, size_target, status_label, message } = result.data;

                // Hitung persentase jika target tersedia
                let percentage = 0;
                if (size_target > 0) {
                    percentage = Math.min((size / size_target) * 100, 100);
                }

                // Update elemen UI
                fileSizeText.textContent = `${size_mb}`;
                progressPercentageText.textContent = `(${percentage.toFixed(0)}%)`;
                progressBarUpdater.style.width = `${percentage.toFixed(2)}%`;

                // Jika selesai
                if (size_target && size >= size_target) {
                    progressBarUpdater.style.width = '100%';
                    progressPercentageText.textContent = '(100%)';

                    timeoutManager.clearTimeoutByKey('downloadPolling');

                    /*if (btnUpdate) {
                        btnUpdate.disabled = false;
                        btnUpdate.innerHTML = `<i class="fas fa-download"></i> Jalankan Update`;
                    }*/

                    if (updateStatus) {
                        updateStatus.textContent = status_label ?? message;
                    }

                    // Reload konten updater setelah selesai download
                    const modalBody = document.querySelector('#updaterContent'); // ganti sesuai id/body target
                    if (modalBody) {
                        timeoutManager.startTimeout('modalUpdaterContentReload', async () => {
                            //await modalUpdaterContent(modalBody); 
                            clearAppCache(true);
                        },0.5);
                    }

                    return;
                }

            } else {
                //infoElement.textContent = result.message;
            }

        } catch (error) {
            ConsoleManager.error('Polling gagal:', error);
        }
    }, 1, {
        log: true,
        repeat: true
    });
}

async function runUpdate() {
    if (!confirm('Yakin ingin menjalankan update?')) return;

    const statusEl = document.getElementById('update-status');
    const logEl = document.getElementById('update-log');
    const btnUpdate = document.getElementById('btn-update');
    
    if (statusEl) statusEl.textContent = 'Memulai update...';
    //if (logEl) logEl.value += 'Memulai update...\n';
    await pollDownloadProgress(); // mulai polling ukuran file
    await fetchStatus();
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

    } catch (err) {
        ConsoleManager.error('Gagal menjalankan update:', err);
        alert('Terjadi kesalahan saat update.');

        if (statusEl) statusEl.textContent = 'Terjadi kesalahan.';
        //if (logEl) logEl.value += `Kesalahan: ${err.message}\n`;
    } 
}

const modalUpdaterContent = async (body) => {
    if (!body) {
        ConsoleManager.warn('Elemen body tidak ditemukan');
        return;
    }

    
    // Reset konten body
    body.innerHTML = '';
    const updaterContent = createElement('div', '', '', { id: 'updaterContent' });
    body.appendChild(updaterContent);

    // Spinner loading utama
    const loadingIndicator = createElement('div', 'text-center', `
        <div class="spinner-border text-default" role="status"></div>
        <div>Mohon tunggu, sedang memuat informasi versi...</div>
    `);
    updaterContent.appendChild(loadingIndicator);

    // Elemen status
    const statusBox = createElement('div', '', '', { id: 'status-box' });
    statusBox.innerHTML = `
        <p><strong>Status:</strong> <span id="update-status" class="text-info">Memuat...</span></p>
        <p><strong>Versi Saat Ini:</strong> <span id="current-version">-</span></p>
        <p><strong>Versi Terbaru:</strong> <span id="latest-version">-</span></p>
        <p><strong>Ukuran:</strong> <span id="size-mb">-</span></p>
        <p><strong>Pre-release:</strong> <span id="pre-release">-</span></p>
    `;

    // Tombol update
    const buttonGroup = createElement('div', 'btn-group');
    const btnUpdate = createElement('button', 'btn btn-primary btn-block', `
        <i class="fas fa-download"></i> Jalankan Update
    `, {
        id: 'btn-update',
        type: 'button'
    });

    // Area log
    const sizeUpdate = createElement('span', 'file-size-update mr-1');
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

            await updateStatusUI(res.data);

            if (!res.data.is_latest) {

                statusBox.innerHTML +=`
                    <div id="download-info">
                        <div class="progress"><div class="progress-bar progress-bar-update"></div></div>
                        <span class="progress-description progress-description-update">
                        <span class="file-size-update mr-1"></span>
                        <span class="progress-percentage-update ml-1"></span>
                        </span>
                    </div>
                `;
                
                updaterContent.appendChild(btnUpdate);

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
        ConsoleManager.error('Gagal mengecek versi:', err);
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
    title: 'Updater v1.0.1',
    loading: true,
    loadingText: 'Mohon tunggu, sedang memuat informasi versi...',
    onLoad: modalUpdaterContent,
    //footer: false
};

