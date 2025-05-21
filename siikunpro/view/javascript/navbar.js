function createNavbar() {
  const navbar = createElement('nav', 'main-header navbar navbar-expand navbar-white navbar-light border-0');
  const navbarBrand = createElement('div', 'navbar-brand text-center', '', { style:'' });
  const brandText = createElement('span', 'brand-text font-weight-light', `${Name} V${Version.replace(/^v/, '').split('-')[0].slice(0, 3)}`);
  const buttonContainer = createElement('div', 'navbar-nav ml-auto');

  // Fetch statistics and update for specific tabs
  const clearCache = async () => {
      const apiUrl = 'index.php?route=obfuscator/clear/cache';
      const obfuscateDirectory = document.querySelector(`[name="${selectDirectory.name}"]`);

      try {
        await fetch(apiUrl, {
            method: 'POST',
            cache: 'no-cache',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                mode: 'one',
                obfuscateDirectory: obfuscateDirectory.value
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (obfuscateDirectory && obfuscateDirectory.value !== '') {
                obfuscateDirectory.dispatchEvent(new Event('change')); // atau gunakan 'input' jika sesuai
            }
            // Memulai timeout
            timeoutManager.startTimeout('fetchStatistics', fetchStatisticsAndUpdate, 1, {
                log: false,
                autoClear: false,
                repeat: false
            });
        })
        .catch(error => {
          ConsoleManager.error('Error fetching statistics:', error);
        });

      } catch (error) {
          ConsoleManager.error('Error fetching statistics:', error);
      }
  };
  const clearAllCache = async () => {
    const apiUrl = 'index.php?route=obfuscator/clear/cache_all';
    const obfuscateDirectory = document.querySelector(`[name="${selectDirectory.name}"]`);

    try {
      await fetch(apiUrl, {
          method: 'POST',
          cache: 'no-cache',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
              mode: 'all',
              obfuscateDirectory: obfuscateDirectory.value
          })
      })
      .then(response => {
          if (!response.ok) {
              throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json();
      })
      .then(data => {
          if (obfuscateDirectory && obfuscateDirectory.value !== '') {
              obfuscateDirectory.dispatchEvent(new Event('change')); // atau gunakan 'input' jika sesuai
          }
          // Memulai timeout
          timeoutManager.startTimeout('fetchStatistics', fetchStatisticsAndUpdate, 1, {
              log: false,
              autoClear: false,
              repeat: false
          });
      })
      .catch(error => {
        ConsoleManager.error('Error fetching statistics:', error);
      });

    } catch (error) {
        ConsoleManager.error('Error fetching statistics:', error);
    }
  };

  // Dropdown dengan beberapa link
  const multiLink = [
      {
        label: 'About',
        href: '#about',
        icon: 'fas fa-info-circle',
        callback: (item, index, event) => {
          const createModalAbout = createModal(modalAboutConfig);
          document.body.appendChild(createModalAbout);
          $(createModalAbout).modal('show');
        }
      },
      {
        label: 'Contact',
        href: '#contact',
        icon: 'fas fa-envelope',
        callback: (item, index, event) => {
          const createModalAbout = createModal(modalAboutConfig);
          document.body.appendChild(createModalAbout);
          $(createModalAbout).modal('show');
        }
      }
  ];

  const dropdownLinks = [
    {
      label: 'Reload',
      href: '#clearCache',
      icon: 'fas fa-sync-alt', // Menambahkan ikon untuk Settings
      callback: clearCache
    },
    {
      label: 'Clear all cache',
      href: '#clearAllCache',
      icon: 'fas fa-cog', // Menambahkan ikon untuk Settings
      callback: clearAllCache
    },
    {
      label: 'Settings',
      href: '#settings',
      icon: 'fas fa-cog', // Menambahkan ikon untuk Settings
      callback: (item, index, event) => {
          const createModalSettings = createModal(modalSettingsConfig);
          document.body.appendChild(createModalSettings);
          $(createModalSettings).modal('show');
      }
    },
    {
      label: 'Tetang Aplikasi',
      href: '#about',
      icon: 'fas fa-info-circle', // Menambahkan ikon untuk Settings
      callback: (item, index, event) => {
        const createModalAbout = createModal(modalAboutConfig);
        document.body.appendChild(createModalAbout);
        $(createModalAbout).modal('show');
      }
    },
  ];
  const dropdownbuttons = [
    {
      label: 'Update',
      icon: 'fas fa-sync-alt',
      onClick: () => {
        const createModalUpdate = createModal(modalUpdateApp);
        document.body.appendChild(createModalUpdate);
        $(createModalUpdate).modal('show');      
      } // Fungsi untuk refresh halaman
    },   
    {
        label: 'Refresh',
        icon: 'fas fa-sync-alt',
        onClick: () => {
          location.reload();
        } // Fungsi untuk refresh halaman
    },
    {
      label: 'More Options',
      icon: 'fas fa-ellipsis-h',
      isDropdown: true,
      links: dropdownLinks
    }
  ];
  // Multi-link tanpa dropdown
  const createMultiLink = createNavLink({
      links: multiLink
  });
  const createDropdownbuttons = createNavLink({
    buttonGroups: [
      {
        isButtonDropdown: true,
        buttons: dropdownbuttons
      }]
  });

  
  buttonContainer.appendChild(createDropdownbuttons);
  //buttonContainer.appendChild(createMultiLink);
  // Menambahkan tombol ke navbar
  buttonContainer.appendChild(createDropdownbuttons);



  navbarBrand.appendChild(brandText);
  navbar.appendChild(navbarBrand);
  navbar.appendChild(buttonContainer);

  return navbar;
}

function createDefaultNavbar() {
  const navbar = createElement('nav', 'main-header navbar navbar-expand navbar-white navbar-light border-0');
  const navbarBrand = createElement('div', 'navbar-brand text-center', '', { style:'' });
  const brandText = createElement('span', 'brand-text font-weight-light', `${Name} V${Version.replace(/^v/, '').split('-')[0].slice(0, 3)}`);
  const buttonContainer = createElement('div', 'navbar-nav ml-auto');

  // Dropdown dengan beberapa link
  const multiLink = [
      {
        label: 'Tentang Aplikasi',
        href: '#about',
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
