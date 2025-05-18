// Membuat preloader dengan spinner
function createPreloader() {
    return new Promise((resolve) => {
        // Create the preloader container
        const preloader = createElement('div', 'preloader flex-column justify-content-center align-items-center');

        // Create the spinner
        const spinner = createElement('div', 'spinner-border', '', { role: 'status' });

        // Create the screen reader text for the spinner
        const srText = createElement('span', 'sr-only', 'Loading...');

        // Append srText to the spinner
        spinner.appendChild(srText);

        // Create the loading text (Lockify V2.0)
        const loadingText = createElement('p', 'mt-2', `${Name} V${Version}`);

        // Append spinner and loading text to the preloader
        preloader.appendChild(spinner);
        preloader.appendChild(loadingText);

        // Resolve the promise with the created preloader
        resolve(preloader);
    });
}

// Membuat Footer
function createFooter() {
    // Create the footer element (Footer container)
    const footer = createElement('footer', 'main-footer border-0');

    // Create the div for the right-side content
    const rightDiv = createElement('div', 'float-right d-none d-sm-block', `<b>Version</b> ${Version}`);

    // Create the strong element for copyright notice
    const copyrightNotice = createElement('strong', '', `Copyright &copy; 2014-2019 <a href="#">${Name}</a>. All rights reserved.`);

    // Append the rightDiv to the footer
    footer.appendChild(rightDiv);
    // Append the copyright notice to the footer
    footer.appendChild(copyrightNotice);

    return footer;
}

// Membuat Content Wrapper
function createContentWrapper() {
    const contentWrapper = createElement('div', 'content-wrapper');
    const contentHeader = createElement('section', 'content-header');
    const contentSection = createElement('section', 'content');

    const containerFluid = createElement('div', 'container-fluid');
    const statisticsRow = createElement('div', 'row mt-3 mb-3', '', { id: 'statistics-container' });
    statisticsRow.innerHTML = '<!-- Statistik akan dimuat di sini -->';

    const tabsAndSidebarRow = createElement('div', 'row mt-3 mb-3');
    const tabsContainer = createElement('div', 'col-md-8', '', { id: 'tabsContainer' });
    tabsContainer.innerHTML = '<!-- Tabs akan dimuat di sini -->';

    // Membuat form dan memasukkan elemen sidebar ke dalam form
    const sidebarForm = createElement('form', 'siikun_form', '', { action: 'index.php?route=obfuscator/process/running', method: 'post', id: 'siikun_form'  });
    const sidebar = createElement('div', 'col-md-4', '', { id: 'sidebar' });
    sidebar.innerHTML = '<!-- Sidebar content -->';
    sidebar.appendChild(sidebarForm); // Memasukkan sidebar ke dalam form

    tabsAndSidebarRow.appendChild(tabsContainer);
    tabsAndSidebarRow.appendChild(sidebar); // Menggantikan sidebar dengan form

    containerFluid.appendChild(statisticsRow);
    containerFluid.appendChild(tabsAndSidebarRow);
    contentSection.appendChild(containerFluid);
    contentWrapper.appendChild(contentHeader);
    contentWrapper.appendChild(contentSection);

    return contentWrapper;
}

// Fungsi untuk memasukkan Content Wrapper ke dalam class wrapper
async function initializeWrapperContent() {
    // Select the wrapper element
    const wrapper = document.querySelector('.wrapper');

    if (wrapper) {
        // Append preloader to the wrapper
        const preloader = await createPreloader();
        wrapper.appendChild(preloader);

        // Setelah preloader berhasil, tambahkan navbar
        const navbar = createNavbar();
        wrapper.appendChild(navbar);

        // Tambahkan Content Wrapper
        const contentWrapper = createContentWrapper();
        wrapper.appendChild(contentWrapper);

        // Tambahkan footer
        const footer = createFooter();
        wrapper.appendChild(footer);
    } else {
        ConsoleManager.error('Element with class "wrapper" not found.');
    }
}
