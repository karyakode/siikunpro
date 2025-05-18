// Helper to create pagination options
const createPaginationOption = (page, isSelected) => ({
    label: `Page ${page}`,
    value: page
});

// Function to create the container structure if not existing
const createPerPageAndPaginationDropdown = (rowId = 'uniqueRowId') => {
    let row = document.getElementById(rowId);

    if (!row) {
        row = createElement('div', 'row my-2', '', { id: rowId });

        const perPageCol = createElement('div', 'col', '', { id: 'elementPerPageDropdown' });
        const paginationCol = createElement('div', 'col', '', { id: 'elementPaginationDropdown' });

        row.appendChild(perPageCol);
        row.appendChild(paginationCol);
    }

    return row;
};

// Populate per-page dropdown options
const populatePerPageDropdown = () => {
    const perPageOptions = [10, 25, 50, 100].map(value => ({
        label: `${value} items per page`,
        value
    }));

    const perPageDropdown = createSelectWithSession('perPageDropdown', perPageOptions, {
        sessionKey: 'perPage',
        useSavedValue: true,
        autoChange: true,
        updateOptions: false,
        onChange: async (selectedValue, isProgrammatic) => {
            const perPage = parseInt(selectedValue, 10);
            const page = 1;  // Reset to page 1 when perPage changes
            updatePaginationOptions({ page: page, perPage: perPage });

            if (!isProgrammatic) {
                updateDataBasedOnSelection(page, perPage);
            }
        }
    });

    const perPageElement = document.getElementById('elementPerPageDropdown');
    perPageElement.innerHTML = '';
    perPageElement.appendChild(perPageDropdown.wrapperElement);
};

// Helper function to fetch and update pagination options for a specific file type
const updatePagination = ({ totalPages = 0, perPage = Session.get('perPage') || 10 } = {}) => {
  populatePaginationDropdown({
      totalPages,
      perPage,
      updateOptions: true
  });
};

// Helper function to fetch and update pagination for a specific file type
const fetchAndUpdatePagination = async ({ fileType, page, perPage, isActive } = {}) => {
    try {

        if (!isActive) return;

        const result = await getFiles(fileType, page, perPage);
        if (result) {
            updatePagination({ totalPages: result.totalPages, perPage: perPage });
        }
    } catch (error) {
        ConsoleManager.error(`Error fetching data for ${fileType}:`, error);
    }
};

// Update pagination options for multiple file types
const updatePaginationOptions = async ({ type, page = 1, perPage = Session.get('perPage') || 10 } = {}) => {
  const fileTypes = type ? [type] : ['writed', 'copied', 'excluded'];
  timeoutManager.startTimeout('timeoutUpdatePaginationOptions', async () => {
    await Promise.all(fileTypes.map(async (fileType) => {
        const tabElement = document.getElementById(`${fileType}FilesContent-tab`);
        if (tabElement) {
            const isActive = tabElement.classList.contains('active');
            await fetchAndUpdatePagination({ fileType, page, perPage, isActive });
        }
    }));
  }, 0.1);
};

// Populate pagination dropdown
const populatePaginationDropdown = (options = {}) => {
    const {
        totalPages = 0,
        currentPage = Session.get('currentPage'),
        updateOptions = true
    } = options;

    const paginationElement = document.getElementById('elementPaginationDropdown');

    // Check if the pagination element exists
    if (!paginationElement) {
        ConsoleManager.warn("Pagination element not found: 'elementPaginationDropdown'");
        return;
    }

    const paginationOptions = totalPages > 0
        ? Array.from({ length: totalPages }, (_, i) => createPaginationOption(i + 1, i + 1 === currentPage))
        : [{ label: 'No pages available', value: '', disabled: true }];

    const paginationDropdown = createSelectWithSession('paginationDropdown', paginationOptions, {
        sessionKey: 'currentPage',
        useSavedValue: false,
        autoChange: false,
        updateOptions,
        onChange: async (selectedValue, isProgrammatic) => {
            if (!isProgrammatic) {
                updateDataBasedOnSelection(parseInt(selectedValue, 10), Session.get('perPage'));
            }
        }
    });

    paginationElement.innerHTML = '';  // Clear existing dropdown
    paginationElement.appendChild(paginationDropdown.wrapperElement);
};
