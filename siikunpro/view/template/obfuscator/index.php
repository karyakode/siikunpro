<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="siikunpro/view/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="siikunpro/view/assets/fonts/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="siikunpro/view/stylesheet/adminlte.min.css">
    <style>
    .brand-text {
      font-size:2.25rem!important;
    }

    body {
      overflow-y: scroll;
    }
    .main-footer,
    .main-header,
    .preloader,
    .content-wrapper {
      background-image: url('siikunpro/view/image/background.png'); /* Ganti dengan path gambar Anda */
      background-repeat: repeat; /* Gambar akan diulang */
      background-attachment: fixed; /* Gambar tetap di tempat saat scroll */
      background-size: auto; /* Ukuran gambar mengikuti ukuran aslinya */
    }

    </style>
    <script>
        const SESSION_KEY = '<?php echo $session_key ?>';
        const destinationPath = `<?php echo $destinationPath ?>`;
        const copyright = `<?php echo $copyright ?>`;
        const Version = `<?php echo $version ?>`;
        const Name = `<?php echo $name ?>`;
    </script>
</head>
<body class="hold-transition layout-top-nav layout-fixed layout-footer-fixed layout-navbar-fixed">
    <!-- JavaScript Libraries -->
    <script src="siikunpro/view/javascript/jquery.min.js"></script>
    <!--script src="siikunpro/view/javascript/popper.min.js"></script-->
    <script src="siikunpro/view/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="siikunpro/view/javascript/adminlte.min.js"></script>
    <script src="siikunpro/view/javascript/crypto-js.min.js"></script>
    <script src="siikunpro/view/javascript/helper.js"></script>
    <script src="siikunpro/view/javascript/statistic.js"></script>
    <script src="siikunpro/view/javascript/pagination.js"></script>
    <script src="siikunpro/view/javascript/lockify.js"></script>
    <script src="siikunpro/view/javascript/tabs.js"></script>
    <script src="siikunpro/view/javascript/plugins.js"></script>
    <script src="siikunpro/view/javascript/sidebar.js"></script>
    <script src="siikunpro/view/javascript/settings.js"></script>
    <script src="siikunpro/view/javascript/about.js"></script>
    <script src="siikunpro/view/javascript/license.js"></script>
    <script src="siikunpro/view/javascript/navbar.js"></script>
    <script src="siikunpro/view/javascript/content.js"></script>
    <script src="siikunpro/view/javascript/update.js"></script>


    <script>



        document.addEventListener('DOMContentLoaded', function () {

          // Membuat elemen wrapper
          const wrapper = createElement('div', 'wrapper');
          // Menambahkan wrapper ke dalam body
          document.body.prepend(wrapper); // Menambahkan di bagian paling atas

          // Jalankan fungsi untuk menginisialisasi konten
          initializeWrapperContent();

          // Memulai timeout
          timeoutManager.startTimeout('startContent', async () => {
            // Pastikan elemen sidebar dan tabs dimuat dengan benar
            const tabs = createNestedBootstrap4TabsWithSession(tabsData, options);
            document.getElementById('tabsContainer').appendChild(tabs);
            document.getElementById('siikun_form').appendChild(sidebarTabsContainer);
            // Pastikan elemen DOM sudah ada sebelum menambahkan konten
            const expertOptions = document.getElementById('expert-options');
            if (expertOptions) {
                expertOptions.prepend(selectObfuscateDirectory.wrapperElement);
                expertOptions.appendChild(inputDestinationPath);
                //expertOptions.appendChild(inputCopyrighth);
                expertOptions.appendChild(checkboxLockDomain);
                expertOptions.appendChild(checkboxLockIP);
                expertOptions.appendChild(checkboxLockDate);
                expertOptions.appendChild(selectChecksumType.wrapperElement);
                expertOptions.appendChild(checkboxObfuscator);
                expertOptions.appendChild(checkboxEncryptor);
                expertOptions.appendChild(checkboxMinify);
                expertOptions.appendChild(processButton);
            }

            const pluginsOptions = document.getElementById('plugins-options');
            if (pluginsOptions) {



              pluginsOptions.prepend(createLabel({
                id:'pluginObfuscators',
                label:'Obfuscator Options'
              }));


              fetchPluginObfuscatorOptions()
              .then(fetchedItems => {
                const pluginObfuscatorsOptions = document.getElementById('contentpluginObfuscators');
                const isChecked = false;
                fetchedItems.forEach((item, index) => {
                  // Definisikan opsi untuk checkbox pertama
                  const multiCheckbox = {
                    id: item.value,
                    checkboxes: [
                      {
                        id: item.value,
                        name: item.value,
                        value: true,
                        label: item.label,
                        isChecked: item.isChecked ? item.isChecked : false,  // Default checked
                        sessionKey: item.value,  // Untuk menyimpan status di session (opsional)
                      },
                    ],
                    onChange: (name, checked) => {
                      //ConsoleManager.log(`Checkbox ${name} is now: ${checked ? 'Checked' : 'Unchecked'}`);
                      // Jika checkbox pertama dicentang, tambahkan input tambahan
                      if (checked) {

                      } else {

                      }
                    },
                    autoChange: false,
                  };

                  pluginObfuscatorsOptions.prepend(createCheckboxGroup(multiCheckbox));
                });
              })
              .catch(err => ConsoleManager.error('Error fetching options:', err));


              pluginsOptions.prepend(selectPluginEncryptions.wrapperElement);
              //pluginsOptions.prepend(selectPluginObfuscators.wrapperElement);

            }

            // Initialize and append dropdown structure if it doesn't exist
            const nestedContainer = document.querySelector('.nested-tab-container');
            if (!nestedContainer) return;

            const dropdownStructure = createPerPageAndPaginationDropdown('customRowId');
            if (!document.getElementById('customRowId') && nestedContainer) {
                nestedContainer.prepend(dropdownStructure);
            }

            // Populate dropdown and clear content with session data for perPage
            populatePerPageDropdown({
              updateOptions: false
            });
            // Clear the pagination dropdown and update UI
            populatePaginationDropdown({
              updateOptions: false
            });

            // Tambahkan logika untuk memuat statistik dan elemen dinamis lainnya
            initializeStatistics();

            loadPlugins();

          }, 0);


        });


    </script>

    <?php
 foreach ($plugins as $plugin) { if ($plugin->isActive()) { echo $plugin->javascript(); } } ?>
</body>
</html>
