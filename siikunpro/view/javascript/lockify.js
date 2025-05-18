
// Element References
const elements = {
    obfuscateDirectory: document.getElementById('obfuscateDirectory'),
    paginationDropdown: document.getElementById('paginationDropdown'),
    perPageDropdown: document.getElementById('perPageDropdown')
};


// Utility function to render file info card
const renderFileInfo = (fileInfo) => {

  // Menghitung MD5 dari fileInfo.name
  const encodedFileName = CryptoJS.MD5(fileInfo.name).toString();

    // Menggunakan helper createAdminCard untuk membuat card
    const card = createAdminCard({
        id: `${encodedFileName}`,
        title: `${fileInfo.name}`,
        bodyContent: `
            <dl class="row">
                <dt class="col-sm-3">File Name</dt>
                <dd class="col-sm-9">${fileInfo.name}</dd>
                <dt class="col-sm-3">File Path</dt>
                <dd class="col-sm-9">${fileInfo.server_path}</dd>
                <dt class="col-sm-3">File Size</dt>
                <dd class="col-sm-9">${fileInfo.size}</dd>
                <dt class="col-sm-3">File Date</dt>
                <dd class="col-sm-9">${fileInfo.date}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">${fileInfo.date}</dd>
            </dl>
        `,
        icon: 'fa-file',
        cardClass: 'card-default collapsed-card text-info',
        isCollapsed: true,
        usePills: false,
        tools: [
            {
                widget: 'collapse',
                icon: 'fa-plus',
                onClick: () => ConsoleManager.log('Card collapsed toggled'),
            }
        ],
    });


    return card.outerHTML; // Pastikan mengembalikan string HTML
};


// Clear session and reload data
const clearSession = async (options = {}) => {
    const {
      resetCount = true,
      resetPagination = true,
      type = false,
      showLoading = true
    } = options;

    const fileTypes = ['writed', 'copied', 'excluded'];

    // Step 1: Reset counts for all file types
    const resetCounts = Object.fromEntries(fileTypes.map(type => [type, 0]));
    if(resetCount) updateCountDisplay(resetCounts);
    if(resetPagination) updatePagination(0);

    // Show loading state for all sections
    if(showLoading) document.querySelectorAll('.section').forEach(section => section.innerHTML = createLoadingContent());

    try {
      timeoutManager.startTimeout('timeoutfetchAndUpdate',() => {
        // Fetch new data based on the updated selection
        updateDataBasedOnSelection();
        // Fetch the actual statistics data and update the display
        fetchAllStatisticsAndUpdate();
        fetchStatisticsAndUpdate();
        updatePaginationOptions({
          type: type
        });
      },0)

    } catch (error) {
        // Re-enable the directory selector in case of error
        document.getElementById('obfuscateDirectory').disabled = false;
    }
};

// Load data based on the selected tab and pagination
const updateDataBasedOnSelection = async (page = 1, perPage = Session.get('perPage')) => {
    const fileTypes = ['writed', 'copied', 'excluded'];

    // Show loading state for all sections before fetching data
    const sections = document.querySelectorAll('.section');
    if (sections.length === 0) {
        ConsoleManager.warn('Warning: No section elements found to display loading state.');
        return;
    }

    sections.forEach(section => {
        section.innerHTML = createLoadingContent();
    });

    // Step 2: Fetch counts and content in parallel using fileTypes.map
    const fetchDataTasks = fileTypes.map(async (type) => {
        try {
            await fetchAndUpdateFiles(type, page, perPage);
        } catch (error) {
            ConsoleManager.error(`Error fetching data for ${type}:`, error);
        }
    });

    // Wait for all data fetching to complete
    await Promise.all(fetchDataTasks);
};

// Fetch files data from the server
const getFiles = async (dataType, currentPage = 1, perPage = Session.get('perPage')) => {
    const changeVal = document.getElementById('obfuscateDirectory').value;
    const directoryUrl = 'index.php?route=obfuscator/file/lists';

    try {
        const response = await fetch(directoryUrl, {
            method: 'POST',
            //cache: 'no-cache',
            body: new URLSearchParams({
                obfuscateDirectory: changeVal,
                dataType: dataType,
                page: currentPage,
                perPage: perPage
            })
        });

        // Extract necessary data from the response
        const { count, totalPages, lists: fileLists } = await response.json();


        // Return the data for UI updates
        return { count, totalPages, currentPage, lists: fileLists };

    } catch (error) {
        ConsoleManager.error(`Error fetching ${dataType} files:`, error);
        return null;
    }
};

// Update the UI based on fetched data
const updateUI = (dataType, count, totalPages, currentPage, perPage, fileLists) => {
    const contentElement = document.querySelector(`#${dataType}FilesContent`);
    const sessionCountKey = `count${capitalize(dataType)}Files`;
    let lists = '';

    // Clear the session count for the current type
    Session.clear(sessionCountKey);

    if (count > 0) {
        // Save file count to session
        Session.set(sessionCountKey, count);

        // Render file info
        fileLists.forEach(({ fileInfo }) => {

            if (fileInfo && fileInfo.name) {
                lists += renderFileInfo(fileInfo);
            } else {
                ConsoleManager.error('Incorrect file structure:', fileInfo);
            }
        });

        // Update content for active tab
        if (contentElement.classList.contains('active')) {
            contentElement.querySelector('.section').innerHTML = lists;
        }

    } else {
        contentElement.querySelector('.section').innerHTML = createEmptyContent();
    }

    // Re-enable UI controls
    document.getElementById('perPageDropdown').disabled = false;
    document.getElementById('paginationDropdown').disabled = false;
    document.getElementById('obfuscateDirectory').disabled = false;

    // Additional UI updates like pagination or count display can be added here
    updateCountDisplay({ [dataType]: count });

};

// Fetch and update files based on selection
const fetchAndUpdateFiles = async (dataType, currentPage = 1, perPage = Session.get('perPage')) => {
    const result = await getFiles(dataType, currentPage, perPage);
    if (result) {
        const { count, totalPages, currentPage, lists: fileLists } = result;
        updateUI(dataType, count, totalPages, currentPage, perPage, fileLists);
    }
};
