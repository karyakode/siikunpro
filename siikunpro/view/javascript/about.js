const modalAboutContent = async (body) => {
    // Simulasi loading untuk 2 detik sebelum mengisi konten
    setTimeout(() => {
        body.innerHTML = '<p>Ini adalah isi dari modal setelah loading!</p>';
    }, 1000);
}

const modalAboutConfig = {
    id: 'modalAbout',
    title: 'About',
    loading: true,
    loadingText: 'Mohon tunggu, sedang memuat...',
    footer: [
        {
            label: 'Batal',
            class: 'btn btn-secondary',
            dismiss: true,
            callback: (event) => {
                ConsoleManager.log('Modal dibatalkan');
            },
        },
        {
            label: 'Simpan',
            class: 'btn btn-primary',
            callback: (event) => {
                ConsoleManager.log('Data disimpan');
            },
        },
    ],
    onLoad: modalAboutContent
};
