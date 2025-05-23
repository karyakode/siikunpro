<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $heading_title ?></title>
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


        // Contoh nilai simulasi (replace sesuai data PHP)
        const error_warning = '<?php echo $error_warning ?>'; // atau teks error
        const action = '<?php echo $action ?>'; // URL form
        const text_setting = '<?php echo $text_setting ?>';
        const text_current = '<?php echo $text_current ?>';
        const text_required = '<?php echo $text_required ?>';
        const text_status = '<?php echo $text_status ?>';
        const text_on = '<?php echo $text_on; ?>'; // Contoh, sesuaikan
        const text_off = '<?php echo $text_off; ?>'; // Contoh, sesuaikan
        const text_writable = `<?php echo $text_writable ?>`;

        // Bagian install PHP
        const text_install_php = '<?php echo $text_install_php; ?>';
        const text_version = '<?php echo $text_version ?>';
        const php_version = '<?php echo $php_version; ?>'; // Contoh, sesuaikan
        const text_global = '<?php echo $text_global ?>';
        const register_globals = '<?php echo $register_globals; ?>'; // Contoh, sesuaikan
        const text_magic = '<?php echo $text_magic ?>';
        const magic_quotes_gpc = '<?php echo $magic_quotes_gpc; ?>'; // Contoh, sesuaikan
        const text_file_upload = '<?php echo $text_file_upload ?>';
        const file_uploads = '<?php echo $file_uploads; ?>'; // Contoh, sesuaikan
        const text_session = '<?php echo $text_session ?>';
        const session_auto_start = '<?php echo $session_auto_start; ?>'; // Contoh, sesuaikan

        // Bagian install extension
        const text_install_extension = `<?php echo $text_install_extension ?>`;
        const text_extension = `<?php echo $text_extension ?>`;
        const text_db = `<?php echo $text_db ?>`;
        const db = `<?php echo $db ?>`;
        const text_gd = `<?php echo $text_gd ?>`;
        const gd = `<?php echo $gd ?>`;
        const text_curl = `<?php echo $text_curl ?>`;
        const curl = `<?php echo $curl ?>`;
        const text_openssl = `<?php echo $text_openssl ?>`;
        const openssl = `<?php echo $openssl ?>`;
        const text_zlib = `<?php echo $text_zlib ?>`;
        const zlib = `<?php echo $zlib ?>`;
        const text_zip = `<?php echo $text_zip ?>`;
        const zip = `<?php echo $zip ?>`;
        const text_mbstring = `<?php echo $text_mbstring ?>`;
        const mbstring = `<?php echo $mbstring ?>`;

        // Bagian install file
        const text_install_file = `<?php echo $text_install_file ?>`;
        const text_file = `<?php echo $text_file ?>`;
        const catalog_config = `<?php echo $catalog_config ?>`;
        const error_catalog_config = `<?php echo $error_catalog_config ?>`;
        const admin_config = `<?php echo $admin_config ?>`;
        const error_admin_config = `<?php echo $error_admin_config ?>`;

        // Bagian install directory
        const text_install_directory = `<?php echo $text_install_directory ?>`;
        const text_directory = `<?php echo $text_directory ?>`;
        const image = `<?php echo $image ?>`;
        const error_image = `<?php echo $error_image ?>`;
        const image_cache = `<?php echo $image_cache ?>`;
        const error_image_cache = `<?php echo $error_image ?>`;
        const image_catalog = `<?php echo $image_catalog ?>`;
        const error_image_catalog = `<?php echo $error_image_catalog ?>`;
        const cache = `<?php echo $cache ?>`;
        const error_cache = `<?php echo $error_cache ?>`;
        const logs = `<?php echo $logs ?>`;
        const error_logs = `<?php echo $error_logs ?>`;
        const download = `<?php echo $download ?>`;
        const error_download = `<?php echo $error_download ?>`;
        const upload = `<?php echo $upload ?>`;
        const error_upload = `<?php echo $error_upload ?>`;
        const modification = `<?php echo $modification ?>`;
        const error_modification = `<?php echo $error_modification ?>`;



    </script>
</head>
<body class="hold-transition layout-top-nav layout-fixed layout-footer-fixed layout-navbar-fixed">
    <!-- JavaScript Libraries -->
    <script src="siikunpro/view/javascript/jquery.min.js"></script>
    <!--script src="siikunpro/view/javascript/popper.min.js"></script-->
    <script src="siikunpro/view/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="siikunpro/view/javascript/adminlte.min.js"></script>
    <script src="siikunpro/view/javascript/helper.js"></script>
    <script src="siikunpro/view/javascript/lockify.js"></script>
    <script src="siikunpro/view/javascript/tabs.js"></script>
    <script src="siikunpro/view/javascript/about.js"></script>
    <script src="siikunpro/view/javascript/license.js"></script>
    <script src="siikunpro/view/javascript/navbar.js"></script>
    <script src="siikunpro/view/javascript/content.js"></script>
    <script src="siikunpro/view/javascript/update.js"></script>


    <script>



    function createRequirementsNavbar() {
      const navbar = createElement('nav', 'main-header navbar navbar-expand navbar-white navbar-light border-0');
      const navbarBrand = createElement('div', 'navbar-brand text-center', '', { style:'' });
      const brandText = createElement('span', 'brand-text font-weight-light', `${Name} V${Version}`);
      const buttonContainer = createElement('div', 'navbar-nav ml-auto');

      // Dropdown dengan beberapa link
      const multiLink = [
         {
          label: 'Update',
          icon: 'fas fa-sync-alt',
          callback: (item, index, event) => {
            const createModalUpdate = createModal(modalUpdateApp);
            document.body.appendChild(createModalUpdate);
            $(createModalUpdate).modal('show');
          }
        },       
        {
          label: 'Refresh',
          icon: 'fas fa-sync-alt',
          callback: (item, index, event) => {
            location.reload();
          }
        },
          {
            label: 'Tentang',
            icon: 'fas fa-info-circle',
            callback: (item, index, event) => {
              const createModalAbout = createModal(modalAboutConfig);
              document.body.appendChild(createModalAbout);
              $(createModalAbout).modal('show');
            }
          },
          {
            label: 'Kontak',
            href: '#contact',
            icon: 'fas fa-envelope',
            callback: (item, index, event) => {
              const createModalAbout = createModal(modalAboutConfig);
              document.body.appendChild(createModalAbout);
              $(createModalAbout).modal('show');
            }
          }
      ];

      // Multi-link tanpa dropdown
      const createMultiLink = createNavLink({
          links: multiLink
      });


      buttonContainer.appendChild(createMultiLink);
      // Menambahkan tombol ke navbar


      navbarBrand.appendChild(brandText);
      navbar.appendChild(navbarBrand);
      navbar.appendChild(buttonContainer);

      return navbar;
    }

    function requirementPHPContent(){

      // create requirement PHP Card

      // === Bagian Fieldset Setting ===
      const fieldset1 = createElement('fieldset');
      const table1 = createElement('table', 'table');

      // Thead
      const thead1 = createElement('thead');
      const trHead1 = createElement('tr');
      const th1 = createElement('th', '', text_setting, { width: '35%' });
      const th2 = createElement('th', '', text_current, { width: '25%' });
      const th3 = createElement('th', '', text_required, { width: '25%' });
      const th4 = createElement('th', 'text-center', text_status, { width: '15%' });

      trHead1.appendChild(th1);
      trHead1.appendChild(th2);
      trHead1.appendChild(th3);
      trHead1.appendChild(th4);
      thead1.appendChild(trHead1);

      // Tbody
      const requirementPHP = createElement('div');
      const tbody1 = createElement('tbody');
      const rowVersion = createElement('tr', '', '', {}, [
        createElement('td', '', text_version),
        createElement('td', '', php_version),
        createElement('td', '', '5.4+'),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            php_version >= '5.4' ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', php_version >= '5.4' ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowGlobal = createElement('tr', '', '', {}, [
        createElement('td', '', text_global),
        createElement('td', '', register_globals ? text_on : text_off),
        createElement('td', '', text_off ),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !register_globals ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !register_globals ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowMagic = createElement('tr', '', '', {}, [
        createElement('td', '', text_magic),
        createElement('td', '', magic_quotes_gpc ? text_on : text_off),
        createElement('td', '', text_off),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !magic_quotes_gpc ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !magic_quotes_gpc ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowFileUploads = createElement('tr', '', '', {}, [
        createElement('td', '', text_file_upload),
        createElement('td', '', file_uploads ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            file_uploads ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', file_uploads ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowSession = createElement('tr', '', '', {}, [
        createElement('td', '', text_session),
        createElement('td', '', session_auto_start ? text_on : text_off),
        createElement('td', '', text_off),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !session_auto_start ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !session_auto_start ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      tbody1.appendChild(rowVersion);
      tbody1.appendChild(rowGlobal);
      tbody1.appendChild(rowMagic);
      tbody1.appendChild(rowFileUploads);
      tbody1.appendChild(rowSession);

      table1.appendChild(thead1);
      table1.appendChild(tbody1);
      fieldset1.appendChild(table1);

      requirementPHP.classList.add('requirementPHP');
      requirementPHP.id = `requirementPHP`;
      requirementPHP.innerHTML = createAdminCard({
          id: `requirementPHPCard`,
          title: `${text_install_php}`,
          bodyContent: fieldset1.outerHTML,
          icon: 'fa-file',
          cardClass: 'card-default',
          isCollapsed: false,
          usePills: false,
          tools: [
              {
                  widget: 'collapse',
                  icon: 'fa-plus'
              }
          ],
      }).outerHTML;


      return requirementPHP;
    }

    function installExtensionContent(){

      // create requirement PHP Card

      // === Bagian Fieldset Setting ===
      const fieldset1 = createElement('fieldset');
      const table1 = createElement('table', 'table');

      // Thead
      const thead1 = createElement('thead');
      const trHead1 = createElement('tr');
      const th1 = createElement('th', '', text_extension, { width: '35%' });
      const th2 = createElement('th', '', text_current, { width: '25%' });
      const th3 = createElement('th', '', text_required, { width: '25%' });
      const th4 = createElement('th', 'text-center', text_status, { width: '15%' });

      trHead1.appendChild(th1);
      trHead1.appendChild(th2);
      trHead1.appendChild(th3);
      trHead1.appendChild(th4);
      thead1.appendChild(trHead1);

      // Tbody
      const installExtension = createElement('div');
      const tbody1 = createElement('tbody');
      const rowDb = createElement('tr', '', '', {}, [
        createElement('td', '', text_db),
        createElement('td', '', db ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            db ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', db ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowGd = createElement('tr', '', '', {}, [
        createElement('td', '', text_gd),
        createElement('td', '', gd ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            gd ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', gd ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowCurl = createElement('tr', '', '', {}, [
        createElement('td', '', text_curl),
        createElement('td', '', curl ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            curl ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', curl ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowOpenssl = createElement('tr', '', '', {}, [
        createElement('td', '', text_openssl),
        createElement('td', '', openssl ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            openssl ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', openssl ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowZlib = createElement('tr', '', '', {}, [
        createElement('td', '', text_zlib),
        createElement('td', '', zlib ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            zlib ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', zlib ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowZip = createElement('tr', '', '', {}, [
        createElement('td', '', text_zip),
        createElement('td', '', zip ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            zip ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', zip ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowMbstring = createElement('tr', '', '', {}, [
        createElement('td', '', text_mbstring),
        createElement('td', '', mbstring ? text_on : text_off),
        createElement('td', '', text_on),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            mbstring ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', mbstring ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      tbody1.appendChild(rowDb);
      tbody1.appendChild(rowGd);
      tbody1.appendChild(rowCurl);
      tbody1.appendChild(rowOpenssl);
      tbody1.appendChild(rowZlib);
      tbody1.appendChild(rowZip);
      tbody1.appendChild(rowMbstring);


      table1.appendChild(thead1);
      table1.appendChild(tbody1);
      fieldset1.appendChild(table1);

      installExtension.classList.add('installExtension');
      installExtension.id = `installExtension`;
      installExtension.innerHTML = createAdminCard({
          id: `installExtensionCard`,
          title: `${text_install_extension}`,
          bodyContent: fieldset1.outerHTML,
          icon: 'fa-file',
          cardClass: 'card-default',
          isCollapsed: false,
          usePills: false,
          tools: [
              {
                  widget: 'collapse',
                  icon: 'fa-plus'
              }
          ],
      }).outerHTML;


      return installExtension;
    }

    function installFileContent(){

      // create requirement PHP Card

      // === Bagian Fieldset Setting ===
      const fieldset1 = createElement('fieldset');
      const table1 = createElement('table', 'table');

      // Thead
      const thead1 = createElement('thead');
      const trHead1 = createElement('tr');
      const th1 = createElement('th', '', text_file, { width: '85%', colspan: '3' });
      const th4 = createElement('th', 'text-center', text_status, { width: '15%' });

      trHead1.appendChild(th1);
      trHead1.appendChild(th4);
      thead1.appendChild(trHead1);

      // Tbody
      const installFile = createElement('div');
      const tbody1 = createElement('tbody');
      const rowVersion = createElement('tr', '', '', {}, [
        createElement('td', '', catalog_config, { colspan: '3'}),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            php_version >= '5.4' ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', php_version >= '5.4' ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowGlobal = createElement('tr', '', '', {}, [
        createElement('td', '', admin_config, { colspan: '3'}),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            php_version >= '5.4' ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', php_version >= '5.4' ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      tbody1.appendChild(rowVersion);
      tbody1.appendChild(rowGlobal);


      table1.appendChild(thead1);
      table1.appendChild(tbody1);
      fieldset1.appendChild(table1);

      installFile.classList.add('installFile');
      installFile.id = `installFile`;
      installFile.innerHTML = createAdminCard({
          id: `installFileCard`,
          title: `${text_install_file}`,
          bodyContent: fieldset1.outerHTML,
          icon: 'fa-file',
          cardClass: 'card-default',
          isCollapsed: false,
          usePills: false,
          tools: [
              {
                  widget: 'collapse',
                  icon: 'fa-plus'
              }
          ],
      }).outerHTML;


      return installFile;
    }

    function installDirectoryContent(){

      // create requirement PHP Card

      // === Bagian Fieldset Setting ===
      const fieldset1 = createElement('fieldset');
      const table1 = createElement('table', 'table');

      // Thead
      const thead1 = createElement('thead');
      const trHead1 = createElement('tr');
      const th1 = createElement('th', '', text_directory, { width: '85%', colspan: '3' });
      const th4 = createElement('th', 'text-center', text_status, { width: '15%' });

      trHead1.appendChild(th1);
      trHead1.appendChild(th4);
      thead1.appendChild(trHead1);

      thead1.appendChild(trHead1);

      // Tbody
      const installDirectory = createElement('div');
      const tbody1 = createElement('tbody');
      const rowVersion = createElement('tr', '', '', {}, [
        createElement('td', '', image, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_image ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_image ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);


      const rowGlobal = createElement('tr', '', '', {}, [
        createElement('td', '', image_cache, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_image_cache ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_image_cache ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowMagic = createElement('tr', '', '', {}, [
        createElement('td', '', cache, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_cache ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_cache ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowFileUploads = createElement('tr', '', '', {}, [
        createElement('td', '', logs, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_logs ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_logs ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);
      const rowSession = createElement('tr', '', '', {}, [
        createElement('td', '', download, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_download ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_download ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowUpload = createElement('tr', '', '', {}, [
        createElement('td', '', upload, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_upload ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_upload ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      const rowModification = createElement('tr', '', '', {}, [
        createElement('td', '', modification, { colspan: '3' }),
        createElement('td', 'text-center', '', {}, [
          createElement('span',
            !error_modification ? 'text-success' : 'text-danger',
            '',
            {},
            [createElement('i', !error_modification ? 'fa fa-check-circle' : 'fa fa-minus-circle')]
          )
        ])
      ]);

      tbody1.appendChild(rowVersion);
      tbody1.appendChild(rowGlobal);
      tbody1.appendChild(rowMagic);
      tbody1.appendChild(rowFileUploads);
      tbody1.appendChild(rowSession);
      tbody1.appendChild(rowUpload);
      tbody1.appendChild(rowModification);

      table1.appendChild(thead1);
      table1.appendChild(tbody1);
      fieldset1.appendChild(table1);

      installDirectory.classList.add('installDirectory');
      installDirectory.id = `installDirectory`;
      installDirectory.innerHTML = createAdminCard({
          id: `installDirectoryCard`,
          title: `${text_install_directory}`,
          bodyContent: fieldset1.outerHTML,
          icon: 'fa-file',
          cardClass: 'card-default',
          isCollapsed: false,
          usePills: false,
          tools: [
              {
                  widget: 'collapse',
                  icon: 'fa-plus'
              }
          ],
      }).outerHTML;


      return installDirectory;
    }


    // Membuat Content Wrapper
    function createRequirementsContentWrapper() {
        const contentWrapper = createElement('div', 'content-wrapper');
        const contentHeader = createElement('section', 'content-header');
        const contentSection = createElement('section', 'content');

        const containerFluid = createElement('div', 'container-fluid');


        // Pesan error
        if (error_warning) {
          const alertIcon = createElement('i', 'fa fa-exclamation-circle');
          const alertText = createElement('span', '', error_warning);
          const closeBtn = createElement('button', 'close', '&times;', {
            type: 'button',
            'data-dismiss': 'alert'
          });

          const alertBox = createElement('div', 'alert alert-danger alert-dismissible', '', {}, [
            alertIcon,
            alertText,
            closeBtn
          ]);
          containerFluid.appendChild(alertBox);
        }

        // Wrapper row dan col
        const row = createElement('div', 'row');
        const col = createElement('div', 'col-sm-12');

        // Form
        const form = createElement('form', '', '', {
          action: action,
          method: 'post',
          enctype: 'multipart/form-data'
        });


        form.appendChild(requirementPHPContent());
        form.appendChild(installExtensionContent());
        form.appendChild(installFileContent());
        form.appendChild(installDirectoryContent());

        // Lengkapi kolom dan form
        col.appendChild(form);
        row.appendChild(col);
        containerFluid.appendChild(row);

        contentSection.appendChild(containerFluid);
        contentWrapper.appendChild(contentHeader);
        contentWrapper.appendChild(contentSection);

        return contentWrapper;
    }

    // Fungsi untuk memasukkan Content Wrapper ke dalam class wrapper
    async function initializeRequirementsWrapperContent() {
        // Select the wrapper element
        const wrapper = document.querySelector('.wrapper');

        if (wrapper) {
            // Append preloader to the wrapper
            const preloader = await createPreloader();
            wrapper.appendChild(preloader);

            // Setelah preloader berhasil, tambahkan navbar
            const navbar = createRequirementsNavbar();
            wrapper.appendChild(navbar);

            // Tambahkan Content Wrapper
            const contentWrapper = createRequirementsContentWrapper();
            wrapper.appendChild(contentWrapper);

            // Tambahkan footer
            const footer = createFooter();
            wrapper.appendChild(footer);
        } else {
            ConsoleManager.error('Element with class "wrapper" not found.');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

      // Membuat elemen wrapper
      const wrapper = createElement('div', 'wrapper');
      // Menambahkan wrapper ke dalam body
      document.body.prepend(wrapper); // Menambahkan di bagian paling atas

      // Jalankan fungsi untuk menginisialisasi konten
      initializeRequirementsWrapperContent();


    });


  </script>

</body>
</html>
