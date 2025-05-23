const modalAboutContent = async (body) => {
    setTimeout(() => {
        body.innerHTML = `
            <div class="text-center mb-4">
                <i class="fas fa-lock fa-3x text-primary"></i>
                <h4 class="mt-2">SiiKUN Pro – PHP Encoder & Obfuscator</h4>
                <p class="text-muted">Solusi profesional untuk mengamankan dan mengoptimalkan kode PHP Anda.</p>
            </div>

            <div class="mb-3">
                <h5><i class="fas fa-shield-alt mr-2 text-info"></i>Fitur Proteksi</h5>
                <ul class="list-unstyled ml-3">
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Fix Domain</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Fix IP</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Fix Date</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Tipe Checksum</li>
                    <li><i class="fas fa-code text-warning mr-2"></i>Obfuscator Source Code</li>
                    <li><i class="fas fa-lock text-danger mr-2"></i>Encryptor Source Code</li>
                    <li><i class="fas fa-compress-alt text-secondary mr-2"></i>Minify Source Code</li>
                </ul>
            </div>

            <div class="mb-3">
                <h5><i class="fas fa-file-contract mr-2 text-warning"></i>Lisensi</h5>
                <p class="text-justify">
                    SiiKUN Pro hanya diperuntukkan untuk penggunaan pribadi atau internal organisasi.
                    Beberapa fitur bersifat premium dan hanya tersedia di versi berbayar. Dilarang menyebarluaskan,
                    memodifikasi, atau mendistribusikan ulang tanpa izin tertulis dari pengembang.
                </p>
            </div>

            <div class="mb-3">
                <h5><i class="fas fa-envelope mr-2 text-primary"></i>Kontak</h5>
                <p>
                    <strong>Pengembang:</strong> Rohmad Kadarwanto<br>
                    <i class="fab fa-whatsapp text-success mr-1"></i> <a href="https://wa.me/6285150768095" target="_blank">0851-5076-8095</a><br>
                    <i class="fab fa-facebook text-primary mr-1"></i> <a href="https://facebook.com/rohmadkadarwanto" target="_blank">@rohmadkadarwanto</a>
                </p>
                <p class="mt-3">
                    <strong>Organisasi:</strong> Karya Kode Indonesia<br>
                    <i class="fab fa-whatsapp text-success mr-1"></i> <a href="https://wa.me/6288706601883" target="_blank">0887-0660-1883</a><br>
                    <i class="fab fa-facebook text-primary mr-1"></i> <a href="https://facebook.com/sebongsoft" target="_blank">@karyakode</a>
                </p>
            </div>

            <div class="mb-3">
                <h5><i class="fab fa-github mr-2 text-dark"></i>GitHub</h5>
                <p>
                    <i class="fas fa-code-branch mr-1"></i> Repository Aplikasi: 
                    <a href="https://github.com/karyakode/siikunpro" target="_blank">karyakode/siikunpro</a><br>
                    <i class="fas fa-users mr-1"></i> Organisasi: 
                    <a href="https://github.com/karyakode" target="_blank">github.com/karyakode</a>
                </p>
            </div>
        `;
    }, 1000);
};

const modalAboutConfig = {
    id: 'modalAbout',
    title: '<i class="fas fa-info-circle mr-2"></i>Tentang',
    loading: true,
    loadingText: 'Mohon tunggu, sedang memuat informasi...',
    onLoad: modalAboutContent
};
