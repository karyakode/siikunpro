
// Configuration for tab container and tools
const tools = [
    { widget: 'collapse', icon: 'fa-minus' }, // Collapse tool
];

const tabsData = [
    {
        label: 'Files',
        id: 'files-content',
        content: ``,
        nestedTabs: [
            {
                label: `File yang dienkripsi (<span id="countWritedFiles">0</span>)`,
                content: `
                  <div class="section pt-4" style="height: 600px; overflow: auto;">

                  </div>
                `,
                id: 'writedFilesContent'
            },
            {
                label: `File yang dicopy (<span id="countCopiedFiles">0</span>)`,
                content: `
                  <div class="section pt-4" style="height: 600px; overflow: auto;">

                  </div>
                `,
                id: 'copiedFilesContent'
            },
            {
                label: `File yang dikecualikan (<span id="countExcludedFiles">0</span>)`,
                content: `
                  <div class="section pt-4" style="height: 600px; overflow: auto;">

                  </div>
                `,
                id: 'excludedFilesContent'
            }
        ]
    },
    {
        label: 'Statistic',
        id: 'statistic-content',
        content: `<div class="row mt-3" id="statistics-row"></div>`
    },
];

// Options for tab container and classes
const options = {
    cardTitle: '',
    tools: tools,
    cardClass: '',
    isCollapsed: false,
    autoClick: false,
    useCard: true,
    usePills: true,
    sessionKey: 'activeTab',
    defaultTab: 'files-content',
    animationClass: 'none',
    nestedContainerClass: 'nested-tab-container',
    onTabClick: handleTabClick
};

// Main function to handle tab click events
function handleTabClick(tab) {
    const activeNestedTab = getActiveNestedTab(tab.id);

    // Determine the type based on the tab's ID
    const type = getTabType(tab.id);

    // Check if statistics tab is clicked
    if (tab.id === 'statistic-content') {
        fetchAllStatisticsAndUpdate();
    } else {
        updatePaginationAndFetchData(activeNestedTab || type);
    }
}

// Helper function to get the active nested tab based on the main tab's ID
function getActiveNestedTab(mainTabId) {
    return Session.get(`activeTab-${mainTabId}`);
}

// Helper function to derive the type from the tab ID
function getTabType(tabId) {
    return tabId.replace('FilesContent', '');
}

// Function to update pagination options and fetch new data
function updatePaginationAndFetchData(type) {

    updatePaginationOptions({
      type: type
    }); // Update pagination for the active tab
    updateDataBasedOnSelection(1, Session.get('perPage')); // Fetch new data
    //clearSession(false, false);


}
