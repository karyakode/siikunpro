const modalSettingsContent = async (body) => {
    // Pastikan body ada
    if (!body) {
        console.warn('Elemen body tidak ditemukan');
        return;
    }

    body.innerHTML = `<div id="obfuscatorConfigForm"></div>`;
    const obfuscatorConfigForm = document.getElementById('obfuscatorConfigForm');

    if (obfuscatorConfigForm) {
        obfuscatorConfigForm.appendChild(settingsTabsContainer);
    } else {
        console.warn('Elemen obfuscatorConfigForm tidak ditemukan');
    }
};

// Data tabs settings yang digunakan dalam modal
const tabsDataSettings = [
    /*{
        label: 'Obfuscator',
        id: 'obfuscator-configuration',
        content: `<div id="dynamicObfuscatorContainer"></div>`
    },*/
    {
        label: 'Directory',
        id: 'directory-configuration',
        content: `
            <div id="dynamicPathsContainer"></div>
            <button id="addPathButton" class="btn btn-success">Add New Path</button>
        `
    }
];

// Membuat settingsTabsContainer dengan helper dan opsi yang ada
const settingsTabsContainer = createNestedBootstrap4TabsWithSession(tabsDataSettings, {
    sessionKey: 'settingsTabs',
    animationClass: '',
    autoClick: true,
    defaultTab: 'obfuscator-configuration',
    onTabClick: async (tab) => {
        ConsoleManager.log(`Tab clicked: ${tab.label}`);

        const configEndpoint = 'index.php?route=api/settings';
        try {
            const data = await fetchConfig(
                tab.id === 'obfuscator-configuration'
                ? `${configEndpoint}/getConfig`
                : `${configEndpoint}/getDirectoryConfig`
            );

            if (data.status === 'success') {
                tab.id === 'obfuscator-configuration' ? populateObfuscator(data.data) : populatePaths(data.data);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error fetching config:', error);
            alert('Failed to load configuration.');
        }
    }
});

async function fetchConfig(endpoint) {
    const response = await fetch(endpoint);
    return response.json();
}

function populateObfuscator(pathsData) {
    clearConfig('#dynamicObfuscatorContainer');
    const container = document.getElementById('dynamicObfuscatorContainer');
    if (container) {
        container.appendChild(createObfuscatorForm(pathsData));
    }
}

function populatePaths(pathsData) {
    clearConfig('#dynamicPathsContainer');
    const container = document.getElementById('dynamicPathsContainer');
    if (container) {
        Object.entries(pathsData).forEach(([key, pathConfig], index) => {
            container.appendChild(createPathForm(key, pathConfig, index + 1));
        });
        document.getElementById('addPathButton')?.addEventListener('click', addNewPath);
    }
}

function addNewPath() {
    const container = document.getElementById('dynamicPathsContainer');
    if (container) {
        const newIndex = container.childElementCount + 1;
        container.appendChild(createPathForm(newIndex, {}, newIndex));
    }
}

function clearConfig(selector) {
    const container = document.querySelector(selector);
    if (container) {
        while (container.firstChild) container.firstChild.remove();
    }
}

function removeConfigPath(selector) {
    const container = document.getElementById(selector);
    if (container) {

        container.remove();
    }
}

// Fungsi untuk membuat form obfuscator
function createObfuscatorForm(pathConfig = {}) {
    const pathForm = document.createElement('div');
    pathForm.classList.add('obfuscation-config');
    pathForm.innerHTML = `
        <div class="form-group">
            <label for="obfuscation_options">Obfuscation Options</label>
            <textarea class="form-control obfuscation-options-field" id="obfuscation_options">${(pathConfig.obfuscation_options || []).join(', ')}</textarea>
        </div>
        <div class="form-group">
            <label for="allowed_mime_types">Allowed Mime Types</label>
            <textarea class="form-control mime-types-field" id="allowed_mime_types">${(pathConfig.allowed_mime_types || []).join(', ')}</textarea>
        </div>
    `;
    return pathForm;
}

function createPathForm(key, pathConfig = {}, index) {
    const pathForm = document.createElement('div');
    pathForm.classList.add('path-config');
    pathForm.id = `${key}`;
    pathForm.innerHTML = `
        <div class="form-group">
            <label for="name_${key}">Name</label>
            <input type="text" class="form-control name-field" data-key="${key}" id="name_${key}" value="${pathConfig.name || ''}">
        </div>
        <div class="form-group">
            <label for="path_${key}">Path</label>
            <input type="text" class="form-control path-field" data-key="${key}" id="path_${key}" value="${pathConfig.path || ''}">
        </div>
        <!--div class="form-group">
            <label for="destination_${key}">Destination</label>
            <input type="text" class="form-control destination-field" data-key="${key}" id="destination_${key}" value="${pathConfig.destination || ''}">
        </div-->
        <div class="form-group">
            <label for="ignored_files_${key}">Ignored files</label>
            <textarea class="form-control ignored-files-field" data-key="${key}" id="ignored_files_${key}">${(pathConfig.ignored_files || []).join(', ')}</textarea>
        </div>
        <div class="form-group">
            <label for="ignored_dirs_${key}">Ignored dirs</label>
            <textarea class="form-control ignored-dirs-field" data-key="${key}" id="ignored_dirs_${key}">${(pathConfig.ignored_dirs || []).join(', ')}</textarea>
        </div>
        <div class="form-group">
            <label for="duplicated_files_${key}">Duplicated files</label>
            <textarea class="form-control duplicated-files-field" data-key="${key}" id="duplicated_files_${key}">${(pathConfig.duplicated_files || []).join(', ')}</textarea>
        </div>
        <div class="form-group">
            <label for="duplicated_dirs_${key}">Duplicated dirs</label>
            <textarea class="form-control duplicated-dirs-field" data-key="${key}" id="duplicated_dirs_${key}">${(pathConfig.duplicated_dirs || []).join(', ')}</textarea>
        </div>
        <button class="btn btn-danger btn-sm" onclick="removeConfigPath('${key}')">Remove</button>
    `;
    return pathForm;
}

function submitConfig() {
    const pathConfigs = {};
    const configs = {};

    // Menangani data konfigurasi untuk setiap path
    document.querySelectorAll('.path-config').forEach((config, index) => {
        const key = `path_${index + 1}`;
        pathConfigs[key] = {
            name: config.querySelector('.name-field')?.value || '',
            path: config.querySelector('.path-field')?.value || '',
            destination: config.querySelector('.destination-field')?.value || '',
            ignored_files: extractFieldData(config, '.ignored-files-field'),
            ignored_dirs: extractFieldData(config, '.ignored-dirs-field'),
            duplicated_files: extractFieldData(config, '.duplicated-files-field'),
            duplicated_dirs: extractFieldData(config, '.duplicated-dirs-field')
        };
    });

    configs.configuration_directory = pathConfigs;

    // Mengambil data konfigurasi obfuscation
    const obfuscationConfig = document.querySelector('.obfuscation-config');
    if (obfuscationConfig) {
        configs.configuration_options = {
            allowed_mime_types: extractFieldData(obfuscationConfig, '.mime-types-field'),
            obfuscation_options: extractFieldData(obfuscationConfig, '.obfuscation-options-field')
        };
    } else {
        console.warn('Element .obfuscation-config tidak ditemukan.');
    }

    // Mengirim konfigurasi ke server
    fetch('index.php?route=api/settings/updateConfig', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(configs)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateDirectoryOptions(selectObfuscateDirectory);
            alert('Configuration updated successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating config:', error);
        alert('Failed to update configuration.');
    });
}

// Fungsi untuk mengekstraksi data field
function extractFieldData(container, selector) {
    const field = container.querySelector(selector);
    return field ? field.value.split(',').map(item => item.trim()).filter(item => item !== "") : [];
}

async function updateDirectoryOptions(selectElement) {
    const options = await fetchServerOptions();
    //selectElement.updateOptions(options);
}


// Konfigurasi modal settings
const modalSettingsConfig = {
    id: 'modalSettings',
    title: 'Configuration',
    loading: true,
    loadingText: 'Mohon tunggu, sedang memuat...',
    footer: [
        { label: 'Batal', class: 'btn btn-secondary', dismiss: true },
        { label: 'Simpan', class: 'btn btn-primary', callback: submitConfig }
    ],
    onLoad: modalSettingsContent
};
