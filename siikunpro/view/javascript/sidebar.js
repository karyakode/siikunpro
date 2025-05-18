// Sidebar configuration tabs
const toolsSidebar = [
    {
      widget: 'collapse',
      icon: 'fa-minus'
    }, // Collapse tool
];

const tabsDataSidebar = [
    {
        label: 'Options',
        id: 'expert-options',
        content: ``,
    },
    {
        label: 'Plugins',
        id: 'plugins-options',
        content: ``
    },
];


// Generate the tab container using the helper function
const sidebarTabsContainer = createNestedBootstrap4TabsWithSession(tabsDataSidebar, {
    useCard: true,            // Use a card container for the tabs
    usePills: true,
    sessionKey: 'sidebarTabs', // Unique session key to store the active tab state
    animationClass: '',   // Animation class for Bootstrap tab transitions
    cardTitle: '', // Title for the card
    cardClass: '', // Custom card classes
    tools: toolsSidebar,      // Sidebar tools like collapse
    isCollapsed: false,       // Do not collapse the card initially
    onTabClick: (tab) => {
        ConsoleManager.log(`Tab clicked: ${tab.label}`);
        // Additional logic can be added here, such as fetching more content or triggering animations.
        if (tab.id === 'plugins-options') {
            // Example: Perform some action when the Plugins tab is clicked
            //alert('Plugins tab clicked!');
        }
    }
});


// Function to fetch options from the server with HTTP (returns JSON)
function fetchPluginEncryptionOptions() {
    return fetch('index.php?route=obfuscator/plugins/lists&type=encryptor') // Replace with your actual API endpoint
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse JSON from the response
        })
        .then(data => {
            // Convert the data to the expected format: {label: 'Option Label', value: 'optionValue'}
            return data.map(item => ({
                label: item.label || item.name, // Adjust to the structure of your API response
                value: item.value || item.id    // Adjust to the structure of your API response
            }));
        })
        .catch(error => {
            ConsoleManager.error('There was a problem with the fetch operation:', error);
            return []; // Return an empty array in case of error
        });
}


const selectPluginEncryptionOptions = {
    sessionKey: 'selectPluginEncryptionSelected',
    //defaultOption: { label: 'Select an option', value: '' },
    name: 'encryptionOptions',
    label: `Encryption Mode`,
    helpText:'(Encryption Mode)',
    autoChange: false,
    useSavedValue: true,
    useSelect2: false,
    serverSide: true,
    updateOptions: true,
    isDisabled: false,
    fetchOptions: fetchPluginEncryptionOptions,
    onChange: (selectedValue, isProgrammatic) => {
        if (isProgrammatic) {

        } else {

        }
    }
}

// Initialize the select element using createSelectWithSession
const selectPluginEncryptions = createSelectWithSession('pluginEncryptions', [], selectPluginEncryptionOptions);




// Function to fetch options from the server with HTTP (returns JSON)
function fetchPluginObfuscatorOptions() {
    return fetch('index.php?route=obfuscator/plugins/lists&type=obfuscator') // Replace with your actual API endpoint
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse JSON from the response
        })
        .then(data => {
            // Convert the data to the expected format: {label: 'Option Label', value: 'optionValue'}
            return data.map(item => ({
                label: item.label || item.name, // Adjust to the structure of your API response
                value: item.value || item.id    // Adjust to the structure of your API response
            }));
        })
        .catch(error => {
            ConsoleManager.error('There was a problem with the fetch operation:', error);
            return []; // Return an empty array in case of error
        });
}


const selectPluginObfuscatorOptions = {
    sessionKey: 'selectPluginObfuscatorSelected',
    //defaultOption: { label: 'Select an option', value: '' },
    name: 'obfuscatorOptions',
    label: `Obfuscators Options`,
    helpText:'(Obfuscators Options)',
    autoChange: false,
    useSavedValue: true,
    useSelect2: false,
    serverSide: true,
    updateOptions: true,
    isDisabled: false,
    fetchOptions: fetchPluginObfuscatorOptions,
    onChange: (selectedValue, isProgrammatic) => {
        if (isProgrammatic) {

        } else {

        }
    }
}

// Initialize the select element using createSelectWithSession
const selectPluginObfuscators = createSelectWithSession('pluginObfuscators', [], selectPluginObfuscatorOptions);





// Function to fetch options from the server with HTTP (returns JSON)
function fetchDirectoryOptions() {
    return fetch('index.php?route=obfuscator/directory/lists') // Replace with your actual API endpoint
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse JSON from the response
        })
        .then(data => {
            // Convert the data to the expected format: {label: 'Option Label', value: 'optionValue'}
            return data.map(item => ({
                label: item.label || item.name, // Adjust to the structure of your API response
                value: item.value || item.id    // Adjust to the structure of your API response
            }));
        })
        .catch(error => {
            ConsoleManager.error('There was a problem with the fetch operation:', error);
            return []; // Return an empty array in case of error
        });
}

const selectDirectory = {
    sessionKey: 'obfuscateDirectorySelected',
    defaultOption: { label: 'Select an option', value: '' },
    name: 'obfuscateDirectory',
    label: `Obfuscate Directory`,
    helpText:'(obfuscateDirectory)',
    autoChange: true,
    useSavedValue: true,
    useSelect2: false,
    serverSide: true,
    updateOptions: true,
    isDisabled: false,
    fetchOptions: fetchDirectoryOptions,
    onChange: (selectedValue, isProgrammatic) => {
        if (isProgrammatic) {
          clearSession();
        } else {
          clearSession();
        }
    }
}

// Initialize the select element using createSelectWithSession
const selectObfuscateDirectory = createSelectWithSession('obfuscateDirectory', [], selectDirectory);



// Definisikan opsi untuk checkbox pertama
const doLockIPOptions = {
  id: 'doLockIp',
  checkboxes: [
    {
      id: 'doLockIp',
      name: 'doLockIp',
      value: 'on',
      label: 'Fix IP',
      isChecked: false,  // Default checked
      //sessionKey: 'doLockIp',  // Untuk menyimpan status di session (opsional)
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
    // Jika checkbox pertama dicentang, tambahkan input tambahan
    if (checked) {
      addAdditionalInput();
    } else {
      removeAdditionalInput();
    }
  },
  autoChange: true,
};

// Definisikan input tambahan untuk "Fix IP"
const inputDoLockIp = createInput({
  type: 'text',
  label: '',
  id: 'lockIp',
  name: 'lockIp',
  placeholder: '93.184.216.34|127.0.0.1',
  helpText: 'Set the code functionality to a special Host IP Address - only if source is <b>encrypted</b>.',
  isRequired: true,
  sessionKey: 'lockIpSession', // Gunakan session key untuk menyimpan nilai
});

// Fungsi untuk menambahkan input tambahan ke DOM setelah checkbox
function addAdditionalInput() {
  // Cari checkbox dengan id yang sesuai
  const checkboxElement = document.getElementById(doLockIPOptions.checkboxes[0].id);
  if (checkboxElement) {
    inputDoLockIp.classList.add('mt-1');

    checkboxElement.parentNode.insertAdjacentElement('afterend', inputDoLockIp); // Menambahkan input setelah checkbox
  } else {
    ConsoleManager.error('Elemen checkbox dengan ID yang sesuai tidak ditemukan.');
  }
}

// Fungsi untuk menghapus input tambahan dari DOM
function removeAdditionalInput() {
  const inputElement = document.getElementById(inputDoLockIp.id);
  if (inputElement && inputElement.parentNode) {
    //inputElement.parentNode.removeChild(inputElement);
    inputElement.remove();
  } else {
    //ConsoleManager.error(`Elemen dengan ID "${inputDoLockIp.id}" tidak ditemukan.`);
  }
}

// Buat checkbox dengan opsi di atas
const checkboxLockIP = createCheckboxGroup(doLockIPOptions);

// Definisikan opsi untuk checkbox pertama
const doLockDomainOptions = {
  id: 'doLockDomain',
  checkboxes: [
    {
      id: 'doLockDomain',
      value: 'on',
      name: 'doLockDomain',
      label: 'Fix Domain',
      isChecked: false,  // Default checked
      //sessionKey: 'doLockDomain',  // Untuk menyimpan status di session (opsional)
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
    // Jika checkbox pertama dicentang, tambahkan input tambahan
    if (checked) {
      addAdditionalInputDomain();
    } else {
      removeAdditionalInputDomain();
    }
  },
  autoChange: true,
};

// Definisikan input tambahan untuk "Fix IP"
const inputDoLockDomain = createInput({
  type: 'text',
  label: '',
  id: 'lockDomain',
  name: 'lockDomain',
  placeholder: 'domain.com|domain.local',
  helpText: 'Set the code functionality to a special Domain Address - only if source is <b>encrypted</b>.',
  isRequired: true,
  sessionKey: 'lockDomainSession', // Gunakan session key untuk menyimpan nilai
});

// Fungsi untuk menambahkan input tambahan ke DOM setelah checkbox
function addAdditionalInputDomain() {
  // Cari checkbox dengan id yang sesuai
  const checkboxElement = document.getElementById(doLockDomainOptions.checkboxes[0].id);
  if (checkboxElement) {
    inputDoLockDomain.classList.add('mt-1');

    checkboxElement.parentNode.insertAdjacentElement('afterend', inputDoLockDomain); // Menambahkan input setelah checkbox
  } else {
    ConsoleManager.error('Elemen checkbox dengan ID yang sesuai tidak ditemukan.');
  }
}

// Fungsi untuk menghapus input tambahan dari DOM
function removeAdditionalInputDomain() {
  const inputElement = document.getElementById(inputDoLockDomain.id);
  if (inputElement && inputElement.parentNode) {
    //inputElement.parentNode.removeChild(inputElement);
    inputElement.remove();
  } else {
    //ConsoleManager.error(`Elemen dengan ID "${inputDoLockIp.id}" tidak ditemukan.`);
  }
}

// Buat checkbox dengan opsi di atas
const checkboxLockDomain = createCheckboxGroup(doLockDomainOptions);


// Definisikan opsi untuk checkbox pertama
const doLockDateOptions = {
  id: 'doLockDate',
  checkboxes: [
    {
      id: 'doLockDate',
      name: 'doLockDate',
      value: 'on',
      label: 'Fix Date',
      isChecked: false,  // Default checked
      //sessionKey: 'doLockDate',  // Untuk menyimpan status di session (opsional)
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
    // Jika checkbox pertama dicentang, tambahkan input tambahan
      if (checked) {
        addAdditionalLockDateInput();
      } else {
        removeAdditionalLockDateInput();
      }


  },
  autoChange: true,
};

// Definisikan input tambahan untuk "Fix Date"
const inputDoLockDate = createInput({
  type: 'date',
  label: '',
  id: 'lockDate',
  name: 'lockDate',
  placeholder: 'Set Expiration Date',
  helpText: 'Blocks the code after the given date &mdash; only if source is <b>encrypted</b>.',
  isRequired: true,
  sessionKey: 'lockDateSession', // Gunakan session key untuk menyimpan nilai
});

// Fungsi untuk menambahkan input tambahan ke DOM setelah checkbox
function addAdditionalLockDateInput() {
  // Cari checkbox dengan id yang sesuai
  const checkboxElement = document.getElementById(doLockDateOptions.checkboxes[0].id);
  if (checkboxElement) {
    inputDoLockDate.classList.add('mt-1');

    checkboxElement.parentNode.insertAdjacentElement('afterend', inputDoLockDate); // Menambahkan input setelah checkbox
  } else {
    //ConsoleManager.error('Elemen checkbox dengan ID yang sesuai tidak ditemukan.');
  }
}

// Fungsi untuk menghapus input tambahan dari DOM
function removeAdditionalLockDateInput() {
  const inputElement = document.getElementById(inputDoLockDate.id);
  if (inputElement && inputElement.parentNode) {
    inputElement.remove(); // Menghapus elemen input langsung
  } else {
    //ConsoleManager.error(`Elemen dengan ID "${inputDoLockDate.id}" tidak ditemukan.`);
  }
}


// Buat checkbox dengan opsi di atas
const checkboxLockDate = createCheckboxGroup(doLockDateOptions);


const checksumTypeId = 'checksumType';
const checksumType = [
    { value: 'md5', label: 'MD5 - 32 karakter' },
    { value: 'sha1', label: 'SHA1 - 40 karakter' },
    { value: 'whirlpool', label: 'Whirlpool - 128 karakter' },
    { value: 'crc32', label: 'CRC32 - 8 karakter' }
];

const selectChecksumTypeOptions = {
    label: 'Tipe Checksum',
    helpText: 'Pilih tipe checksum yang ingin Anda gunakan.',
    feedback: 'Silakan pilih tipe checksum yang valid.',
    name: checksumTypeId,
    autoChange: true,
    onChange: (selectedValue, isProgrammatic) => {
      if (isProgrammatic) {

      } else {

      }
    },
    isDisabled: false
};
const selectChecksumType = createSelectWithSession(checksumTypeId, checksumType, selectChecksumTypeOptions);

const inputDestination = {
    type: 'hidden',
    //label: 'Destination Path',
    id: 'destinationPath',
    name: 'destinationPath',
    //placeholder: 'Destination Path',
    //helpText: 'We\'ll never share your email with anyone else.',
    isRequired: true,
    value: destinationPath,
    sessionKey: 'destinationPathSession', // Use session key for storing value
}

// Example usage
const inputDestinationPath = createInput(inputDestination);


// Example usage
const inputCopyrighth = createTextarea({
    type: 'text',
    label: 'Add a Copyright Tag',
    id: 'copyright',
    name: 'copyright',
    placeholder: 'Add a Copyright Tag',
    helpText: 'In the header directly after the PHP start tag.',
    isRequired: true,
    value: copyright,
    sessionKey: 'copyrightSession', // Use session key for storing value
});


// Definisikan opsi untuk checkbox pertama
const encryptorOptions = {
  id: 'encryptor',
  checkboxes: [
    {
      id: 'encryptor',
      name: 'encryptor',
      value: 'on',
      label: 'Encryptor source code',
      isChecked: true,  // Default checked
      sessionKey: 'encryptor',  // Untuk menyimpan status di session (opsional)
      //helpText: 'Encryptor source code makes a smaller payload size.',
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);

  },
  autoChange: false,
};

// Buat checkbox dengan opsi di atas
const checkboxEncryptor = createCheckboxGroup(encryptorOptions);


// Definisikan opsi untuk checkbox pertama
const obfuscatorOptions = {
  id: 'obfuscator',
  checkboxes: [
    {
      id: 'obfuscator',
      name: 'obfuscator',
      value: 'on',
      label: 'Obfuscator source code',
      isChecked: true,  // Default checked
      sessionKey: 'obfuscator',  // Untuk menyimpan status di session (opsional)
      //helpText: 'Obfuscator source code makes a smaller payload size.',
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);

  },
  autoChange: false,
};

// Buat checkbox dengan opsi di atas
const checkboxObfuscator = createCheckboxGroup(obfuscatorOptions);




// Definisikan opsi untuk checkbox pertama
const minifyOptions = {
  id: 'minify',
  checkboxes: [
    {
      id: 'minify',
      name: 'minify',
      value: 'on',
      label: 'Minify source code',
      isChecked: true,  // Default checked
      sessionKey: 'minify',  // Untuk menyimpan status di session (opsional)
      helpText: 'Minify source code makes a smaller payload size.',
    },
  ],
  onChange: (name, checked) => {
    //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);

  },
  autoChange: false,
};

// Buat checkbox dengan opsi di atas
const checkboxMinify = createCheckboxGroup(minifyOptions);




const processStages = ['removeFiles','removeDir','createDir','process']; // Daftar proses berurutan

// Membuat tombol "Process"
const processButton = createButton({
    type: 'submit',
    id: 'button-obfuscate',
    text: 'Process',
    classNames: 'btn-primary optional mt-2',
    attributes: { name: 'process', value: processStages[0] }, // Nilai awal 'remove'
    onClick: async (event) => {
        event.preventDefault(); // Mencegah form untuk disubmit secara default
        toggleLoadingButton(event.target, true); // Tampilkan animasi loading

        //fetchStatisticsAndUpdate(1, true, false); // Menjalankan fetch dengan repeat

        // Memulai timeout
        timeoutManager.startTimeout('fetchStatistics', fetchStatisticsAndUpdate, 1, {
            //retryCount: 3,
            log: false,
            //retryDurations: [2, 3, 5], // Durasi untuk setiap retry
            autoClear: false,
            repeat: true
        });

        // Menunggu fetchStatisticsAndUpdate selesai
        await new Promise(resolve => {
          timeoutManager.startTimeout('resolveFetchStatisticsAndUpdate', resolve,0)
        }); // Tunggu sebelum melanjutkan ke proses berikutnya

        // Set status ke 'running' dan mulai proses
        Session.set('process_status', 'running');
        await processStageForm(event, 0); // Mulai dari proses pertama (index 0)


    },
    isBlock: true,
    icon: 'fa fa-cogs'
});


// Fungsi untuk menampilkan/menghapus animasi loading pada tombol
const toggleLoadingButton = (button, isLoading) => {
    if (isLoading) {
        button.disabled = true; // Nonaktifkan tombol saat loading
        button.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Processing...`; // Tampilkan ikon spinner
    } else {
        button.disabled = false; // Aktifkan kembali tombol setelah proses selesai
        button.innerHTML = `<i class="fa fa-cogs"></i> Process`; // Kembalikan ikon dan teks asli
    }
};

// Fungsi untuk mengecek apakah proses boleh dilanjutkan
const isProcessRunning = () => {
    return Session.get('process_status') === 'running'; // Cek status dari Session
};

// Definisikan fungsi processStageForm untuk menjalankan semua proses berurutan
const processStageForm = async (event, stageIndex) => {
    // Jika status proses 'stopped', hentikan proses
    if (!isProcessRunning()) {
        ConsoleManager.log('Process has been stopped.');
        toggleLoadingButton(event.target, false); // Kembalikan tombol ke keadaan semula
        return;  // Keluar dari proses jika dihentikan
    }

    const form = event.target.closest('form'); // Mendapatkan form terdekat dari tombol yang diklik
    const formData = new FormData(form); // Mengambil semua data dari form

    const currentProcess = processStages[stageIndex]; // Mengambil proses saat ini berdasarkan stageIndex
    formData.append('process', currentProcess); // Menambahkan key 'process' dengan value saat ini

    // Debugging: Menampilkan proses yang sedang dijalankan
    ConsoleManager.log(`Executing process: ${currentProcess}`);

    // Mengirim data ke server menggunakan fetch
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
        });

        if (response.ok) {
            const result = await response.json();
            ConsoleManager.log(`Process ${currentProcess} completed: ${result.message}`);

            // Jika masih ada proses berikutnya, lanjutkan ke proses selanjutnya
            if (stageIndex < processStages.length - 1) {
                await processStageForm(event, stageIndex + 1); // Lanjut ke proses berikutnya
            } else {
                // Selesai semua proses, set status ke 'stopped' dan kembalikan tombol ke keadaan semula
                Session.set('process_status', 'stopped');
                toggleLoadingButton(event.target, false);
                //alert('All processes completed.');

                // Clear session di server
                await fetch(form.action, {
                    method: 'POST',
                    body: new URLSearchParams({ process: 'clear_process_status' }), // Tambahkan permintaan untuk membersihkan sesi
                });

            }
        } else {
            ConsoleManager.error('Network response was not ok.','');
        }

        timeoutManager.clearTimeoutByKey('fetchStatistics', 1000);

    } catch (error) {
        ConsoleManager.error('There has been a problem with your fetch operation:', error);
        //alert('Error submitting form!'); // Menampilkan pesan kesalahan

        // Kembalikan tombol ke keadaan semula jika terjadi error
        toggleLoadingButton(event.target, false);

        timeoutManager.clearTimeoutByKey('fetchStatistics');
    }


};

// Example usage for a group of checkboxes with session storage
const checkboxGroup = createCheckboxGroup({
    checkboxes: [
        { label: 'Option 1', id: 'option1-checkbox', name: 'options', value: '1', isChecked: false, sessionKey: 'option1' },
        { label: 'Option 2', id: 'option2-checkbox', name: 'options', value: '2', isChecked: true, sessionKey: 'option2' },
        { label: 'Option 3', id: 'option3-checkbox', name: 'options', value: '3', isChecked: false, sessionKey: 'option3' }
    ],
    onChange: (name, checked) => {
        ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
    },
    autoChange: false // Automatically trigger onChange
});

// Example usage for a group of radio buttons with session storage
const radioGroup = createRadioGroup({
    radios: [
        { label: 'Choice A', id: 'choiceA-radio', name: 'choices', value: 'A', isChecked: true, sessionKey: 'choices' },
        { label: 'Choice B', id: 'choiceB-radio', name: 'choices', value: 'B', isChecked: false, sessionKey: 'choices' },
        { label: 'Choice C', id: 'choiceC-radio', name: 'choices', value: 'C', isChecked: false, sessionKey: 'choices' }
    ],
    onChange: (name, checked) => {
        ConsoleManager.log(`Radio ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
    },
    autoChange: false // Automatically trigger onChange
});


// Example usage
const textareaField = createTextarea({
    label: 'Description',
    id: 'description-textarea',
    name: 'description',
    placeholder: 'Enter a description',
    value: '',
    rows: 4,
    helpText: 'Provide detailed information.',
    isRequired: true,
    isDisabled: false,
    sessionKey: 'descriptionSession', // Use session key for storing value
});


// Usage example
const folderPickerElement = createFolderPicker({
    id: 'folderPicker',
    name: 'selectedFolder',
    label: 'Pilih Folder',
    sessionKey: 'chosenFolder',
    feedback: 'Silakan pilih folder yang valid.',
    helpText: 'Ini adalah help text untuk pemilihan folder.',
    //displayFullPath: true, // Set to true to show full path, false to show folder name
    defaultDirectory: destinationPath,
    onChange: (folderPath) => ConsoleManager.log('Selected folder:', folderPath)
});

const selectId = 'countrySelect';
const countries = [
    { value: 'id', label: 'Indonesia' },
    { value: 'us', label: 'United States' },
    { value: 'fr', label: 'France' },
    { value: 'jp', label: 'Japan' }
];

const selectOptions = {
    label: 'Negara',
    helpText: 'Pilih negara Anda.',
    feedback: 'Silakan pilih negara yang valid.',
    defaultOption: { value: '', label: 'Pilih...' },
    isDisabled: false
};
