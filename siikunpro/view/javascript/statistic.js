
// Fungsi untuk menampilkan spinner
const showSpinner = (containerId) => {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...'; // Menampilkan spinner
    }
};

// Fungsi untuk menyembunyikan spinner
const hideSpinner = (containerId) => {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = ''; // Mengosongkan kontainer untuk menyembunyikan spinner
    }
};



const renderStatistics = (containerId, statisticsConfig, fetchDataFn) => {
    const container = document.getElementById(containerId);

    if (!container) {
        ConsoleManager.error(`Container with ID "${containerId}" not found.`);
        return; // Exit the function if the container doesn't exist
    }

    container.innerHTML = ''; // Clear the container before rendering

    statisticsConfig.forEach(stat => {
        const infoBoxElement = createInfoBox(stat);
        container.appendChild(infoBoxElement);
    });

    // Fetch the actual statistics data and update the display
    fetchDataFn();
};


// Fetch statistics and update for "All Statistics"
const fetchAllStatisticsAndUpdate = async () => {
    //const selectElement = document.querySelector(`[name="${selectDirectory?.name}"]`);
    const selectElement = document.querySelector(`[name="${selectDirectory.name}"]`);
    if (!selectElement) {
        ConsoleManager.error('Error: selectDirectory element not found.');
        return;
    }

    const obfuscateDirectory = selectElement.value;
    const apiUrl = 'index.php?route=obfuscator/statistics/all';

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            cache: 'no-cache',
            body: new URLSearchParams({ mode: 'all', obfuscateDirectory: obfuscateDirectory })
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json() || {};

        const statsData = {
            'all-statistic-file': {
                totalFiles: (data.totalFiles || 0),
                totalFileSizeMB: (data.totalFileSizeMB || 0 + ' B'),
                progress: Math.round((data.totalFiles / (data.totalFiles || 1)) * 100)
            },
            'all-statistic-excluded': {
                totalFiles: (data.ignoredFiles || 0),
                totalFileSizeMB: (data.ignoredFileSizeMB || 0  + ' B'),
                progress: Math.round((data.ignoredFiles / (data.totalFiles || 1)) * 100)
            },
            'all-statistic-moved': {
                totalFiles: (data.duplicatedFiles || 0),
                totalFileSizeMB: (data.duplicatedFileSizeMB || 0 + ' B'),
                progress: Math.round((data.duplicatedFiles / (data.totalFiles || 1)) * 100)
            },
            'all-statistic-written': {
                totalFiles: (data.writingFiles || 0),
                totalFileSizeMB: (data.writingFileSizeMB || 0 + ' B'),
                progress: Math.round((data.writingFiles / (data.totalFiles || 1)) * 100)
            }
        };

        updateStatisticsDisplay(statsData);
    } catch (error) {
        ConsoleManager.error('Error fetching all statistics:', error);
    }
};


// Fetch statistics and update for specific tabs
const fetchStatisticsAndUpdate = async () => {
    const selectElement = document.querySelector(`[name="${selectDirectory.name}"]`);

    if (!selectElement) {
        ConsoleManager.error('Error: selectDirectory element not found.');
        return;
    }

    const obfuscateDirectory = selectElement.value;
    const apiUrl = 'index.php?route=obfuscator/statistics/index';

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            cache: 'no-cache',
            body: new URLSearchParams({ mode: 'one', obfuscateDirectory: obfuscateDirectory })
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json() || {};

        const statsData = {
            'statistic-file': {
                totalFiles: (data.totalFiles || 0),
                totalFileSizeMB: (data.totalFileSizeMB || 0 + ' B'),
                progress: Math.round((data.totalFiles / (data.totalTotalFiles || 1)) * 100) // Menghitung progres berdasarkan jumlah total file
            },
            'statistic-copied': {
                totalFiles: (data.duplicatedFiles || 0),
                totalFileSizeMB: (data.duplicatedFileSizeMB || 0 + ' B'),
                progress: Math.round((data.duplicatedFiles / (data.totalDuplicatedFiles || 1)) * 100) // Menghitung progres berdasarkan jumlah total file yang disalin
            },
            'statistic-written': {
                totalFiles: (data.writingFiles || 0),
                totalFileSizeMB: (data.writingFileSizeMB || 0 + ' B'),
                progress: Math.round((data.writingFiles / (data.totalWritingFiles || 1)) * 100) // Menghitung progres berdasarkan jumlah total file yang ditulis
            }
        };

        updateStatisticsDisplay(statsData);
    } catch (error) {
        ConsoleManager.error('Error fetching statistics:', error);
    }
};



// Function to update statistics with fetched data
const updateStatisticsDisplay = (data) => {
    Object.entries(data).forEach(([id, stat]) => {
        const infoBox = document.getElementById(id);
        if (infoBox) {
            const number = infoBox.querySelector('.info-box-number');
            const progressBar = infoBox.querySelector('.progress-bar');
            const progressDescription = infoBox.querySelector('.progress-description');

            // Update number and progress bar if they exist
            if (number) number.textContent = `${stat.totalFiles} File`;
            progressBar.style.width = `${ progressBar ? stat.progress || 0 : 0 }%`;

            // Clear previous content of progressDescription
            progressDescription.innerHTML = '';

            // Create and append elements for total file size and progress using createElement
            const sizeElement = createElement('span', 'file-size mr-1', `${stat.totalFileSizeMB}`);
            const progressElement = createElement('span', 'progress-percentage ml-1', `(${stat.progress || 0}%)`);

            // Append the new elements to progressDescription
            progressDescription.appendChild(sizeElement);
            if (progressBar) progressDescription.appendChild(progressElement);
        }
    });
};


// Initializing the statistics rendering with dynamic configurations
const initializeStatistics = () => {
    const allStatisticsConfig = [
        { id: 'all-statistic-file', class: 'purple', title: 'Statistik File', colSize: 6, skin: 'purple', isGradient: true, boxType: 'info-box' },
        { id: 'all-statistic-excluded', class: 'warning', title: 'File yang dikecualikan', colSize: 6, skin: 'warning', isGradient: true, boxType: 'info-box', link: '#more-info-excluded' },
        { id: 'all-statistic-moved', class: 'maroon', title: 'File yang dipindahkan', colSize: 6, skin: 'maroon', isGradient: true, boxType: 'info-box', link: '#more-info-moved' },
        { id: 'all-statistic-written', class: 'navy', title: 'File yang ditulis', colSize: 6, skin: 'navy', isGradient: true, boxType: 'info-box' }
    ];

    const specificStatisticsConfig = [
        { id: 'statistic-file', class: 'danger', title: 'Statistik File', colSize: 4, skin: 'danger', showProgress: true, isGradient: true, boxType: 'info-box', link: '#more-info-file' },
        { id: 'statistic-copied', class: 'info', title: 'File yang tercopy', colSize: 4, skin: 'info', showProgress: true, isGradient: true, boxType: 'info-box', link: '#more-info-copied' },
        { id: 'statistic-written', class: 'success', title: 'File yang tertulis', colSize: 4, skin: 'success', showProgress: true, isGradient: true, boxType: 'info-box' }
    ];

    renderStatistics('statistics-row', allStatisticsConfig, fetchAllStatisticsAndUpdate);
    renderStatistics('statistics-container', specificStatisticsConfig, fetchStatisticsAndUpdate);
};
