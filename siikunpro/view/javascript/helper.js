// Console Management Helper
// Console Management Helper with Error Handling
const ConsoleManager = (() => {
    let mode = 'production'; // Default mode set to 'dev'

    const setMode = (newMode) => {
        mode = newMode === 'production' ? 'production' : 'dev';
    };

    const log = (...args) => {
        if (mode === 'dev') console.log(...args);
    };

    const warn = (...args) => {
        if (mode === 'dev') console.warn(...args);
    };

    const error = (...args) => {
        if (mode === 'dev') console.error(...args);
    };

    const info = (...args) => {
        if (mode === 'dev') console.info(...args);
    };

    const throwError = (condition, errorMessage) => {
        if (!condition) {
            const errorInstance = new Error(errorMessage);
            if (mode === 'dev') {
                console.error(errorInstance);
            }
            throw errorInstance; // This will stop the function execution and propagate the error
        }
    };

    return {
        setMode,
        log,
        warn,
        error,
        info,
        throwError,
    };
})();

// Usage of throwError for error handling
/*const sessionKey = ''; // Example of an invalid session key
ConsoleManager.throwError(
    sessionKey && typeof sessionKey === 'string',
    'Session key must be a non-empty string.'
);*/

//ConsoleManager.setMode('production'); // Set mode to 'production' to disable console output

// Error Handling Helper with conditional and unconditional throwError
const ErrorManager = (() => {
    let mode = 'production'; // Default mode is 'dev'

    // Set the mode: 'dev' for development, 'production' to disable console logs
    const setMode = (newMode) => {
        mode = newMode === 'production' ? 'production' : 'dev';
    };

    // Conditional throwError: Throws error if condition is false
    const throwErrorIf = (condition, errorMessage) => {
        if (!condition) {
            const errorInstance = new Error(errorMessage);
            if (mode === 'dev') {
                console.error(`[ErrorManager] ${errorInstance.message}`);
                throw errorInstance;
            }
        }
    };

    // Unconditional throwError: Always throws the error
    const throwError = (errorMessage) => {
        const errorInstance = new Error(errorMessage);
        if (mode === 'dev') {
            console.error(`[ErrorManager] ${errorInstance.message}`);

            throw errorInstance;
        }
    };

    // Example helper for session key validation with condition check
    const checkSessionKey = (sessionKey) => {
        throwErrorIf(
            sessionKey && typeof sessionKey === 'string' && sessionKey.trim() !== '',
            'Session key must be a non-empty string.'
        );
    };

    return {
        setMode,
        throwErrorIf,
        throwError,
        checkSessionKey,
    };
})();


/*// Conditional throw example: Throws only if condition fails
try {
    ErrorManager.throwErrorIf(5 > 10, 'This condition should fail.');
} catch (error) {
    console.error('Caught error:', error.message);
}

// Unconditional throw example: Always throws the error
try {
    ErrorManager.throwError('This will always throw an error.');
} catch (error) {
    console.error('Caught error:', error.message);
}

// Session key validation example
try {
    ErrorManager.checkSessionKey(''); // This will throw an error
} catch (error) {
    console.error('Caught error:', error.message);
}
*/


// Example Usage
ConsoleManager.setMode('dev'); // Set mode to 'dev' or 'production'
ErrorManager.setMode('dev'); // Set to 'dev' or 'production' to control console output


/**
 * Simple session management utility for local storage operations.
 */
const Session = (() => {

    // Private helper to validate the key
    const validateKey = (key) => {
        if (typeof key !== 'string' || key.trim() === '') {
            ErrorManager.throwError('Session key must be a non-empty string.');
        }
    };

    return {
        /**
         * Set a value in local storage by key
         * @param {string} key - The local storage key
         * @param {string} value - The value to store
         */
        set(key, value) {
            validateKey(key);
            localStorage.setItem(key, value);
        },

        /**
         * Get a value from local storage by key
         * @param {string} key - The local storage key
         * @param {*} defaultValue - Default value to return if the key doesn't exist
         * @returns {*} - The stored value or the default value
         */
        get(key, defaultValue = null) {
            validateKey(key);
            const value = localStorage.getItem(key);
            return value !== null ? value : defaultValue;
        },

        /**
         * Clear a specific key from local storage
         * @param {string} key - The local storage key to remove
         */
        clear(key) {
            validateKey(key);
            localStorage.removeItem(key);
        },

        /**
         * Clear multiple keys from local storage
         * @param {string[]} keys - An array of keys to remove
         */
        clearMultiple(keys = []) {
            if (!Array.isArray(keys)) {
                ErrorManager.throwError('Keys must be an array of strings.');
            }
            keys.forEach((key) => {
                this.clear(key);
            });
        }
    };
})();

const timeoutManager = (() => {
    const timeouts = {};

    const startTimeout = (key, callback, timeoutDuration, options = {}) => {
        const { retryCount = 0, log = false, chainedCallback = null, retryDurations = [], autoClear = true, repeat = false } = options;

        let currentRetry = 0;
        const isTimeoutArray = Array.isArray(timeoutDuration);
        const durations = isTimeoutArray ? timeoutDuration.map(d => d * 1e3) : [timeoutDuration * 1e3];

        const logEvent = (message) => {
            if (log) {
                ConsoleManager.log(`[Timeout - ${key}]: ${message}`);
            }
        };

        const timeoutCallback = () => {
            try {
                callback();
                logEvent('Executed successfully.');

                if (chainedCallback) chainedCallback();

                // Jika repeat diaktifkan, kita akan memulai timeout baru
                if (repeat) {
                    logEvent('Repeating callback...');
                    timeouts[key] = setTimeout(timeoutCallback, durations[0]);
                }

                // Jika autoClear diaktifkan, hapus timeout setelah eksekusi
                if (autoClear && !repeat) {
                    clearTimeoutByKey(key, durations[0]);
                }
            } catch (error) {
                logEvent(`Error occurred: ${error.message}`);

                if (currentRetry < (retryDurations.length - 1 || retryCount)) {
                    currentRetry++;
                    const nextTimeout = durations[currentRetry] || durations[0];
                    logEvent(`Retrying... Attempt ${currentRetry}. Next timeout: ${nextTimeout}ms.`);
                    timeouts[key] = setTimeout(timeoutCallback, nextTimeout);
                } else {
                    logEvent('Max retry attempts reached.');
                    clearTimeoutByKey(key, durations[0]);
                }
            }
        };

        if (timeouts[key]) {
            clearTimeout(timeouts[key]);
            logEvent('Previous timeout cleared.');
        }

        timeouts[key] = setTimeout(timeoutCallback, durations[0]);
        logEvent(`Started timeout with ${durations[0]}ms. Current timeouts: ${JSON.stringify(timeouts)}`);
    };

    const clearTimeoutByKey = (key, timeoutDuration = 0) => {
        if (timeouts[key]) {
            //const remainingTime = clearTimeout(timeouts[key]);
            //const timeoutDuration = Math.round((timeouts[key]._idleStart + timeouts[key]._idleTimeout) - Date.now());
            setTimeout(() => {
                clearTimeout(timeouts[key]);
                delete timeouts[key];
                ConsoleManager.log(`[Timeout - ${key}]: Cleared after ${timeoutDuration}ms.`);
            }, timeoutDuration);
        } else {
            ConsoleManager.log(`[Timeout - ${key}]: No active timeout to clear.`);
        }
    };

    return { startTimeout, clearTimeoutByKey };
})();

const intervalManager = (() => {
    const intervals = {};

    // Start a new interval with logging, retry, and chaining callbacks
    const start = (key, callback, intervalTime, options = {}) => {
        const { retryCount = 0, log = true, chainedCallback = null } = options;

        let currentRetry = 0;

        // Logging function
        const logEvent = (message) => {
            if (log) {
                ConsoleManager.log(`[Interval - ${key}]: ${message}`);
            }
        };

        // Main callback handler with retry and callback chaining
        const intervalCallback = () => {
            try {
                callback();
                logEvent('Executed successfully.');

                // Chain callback if provided
                if (chainedCallback) chainedCallback();
            } catch (error) {
                logEvent(`Error occurred: ${error.message}`);

                // Retry logic
                if (currentRetry < retryCount) {
                    currentRetry++;
                    logEvent(`Retrying... Attempt ${currentRetry}/${retryCount}`);
                } else {
                    stop(key);
                    logEvent('Max retry attempts reached. Stopping interval.');
                }
            }
        };

        // Clear any existing interval with the same key
        if (intervals[key]) {
            clearInterval(intervals[key]);
            logEvent('Previous interval cleared.');
        }

        // Start the interval
        intervals[key] = setInterval(intervalCallback, intervalTime);
        logEvent(`Started interval with ${intervalTime}ms.`);
    };

    // Stop a specific interval by key
    const stop = (key) => {
        if (intervals[key]) {
            clearInterval(intervals[key]);
            delete intervals[key];
            ConsoleManager.log(`[Interval - ${key}]: Stopped.`);
        }
    };

    // Stop all intervals
    const stopAll = () => {
        Object.keys(intervals).forEach((key) => {
            clearInterval(intervals[key]);
            delete intervals[key];
            ConsoleManager.log(`[Interval - ${key}]: Stopped.`);
        });
    };

    // Check if an interval is running for a specific key
    const isRunning = (key) => !!intervals[key];

    return { start, stop, stopAll, isRunning };
})();

/**
 * Helper function to create an HTML element.
 * @param {string} tag - HTML tag to create.
 * @param {string} classNames - Classes to add to the element.
 * @param {string} innerHTML - Inner text or HTML to add inside the element.
 * @param {Object} [attributes={}] - Object containing attributes to set on the element.
 * @returns {HTMLElement} - The created HTML element.
 */
 const createElement = (tag, classNames = '', innerHTML = '', attributes = {}, children = []) => {
   const element = document.createElement(tag);

   // Tangani classNames, bisa string atau array
   if (classNames) {
     if (Array.isArray(classNames)) {
       element.className = classNames.join(' ');
     } else {
       element.className = classNames;
     }
   }

   if (innerHTML) element.innerHTML = innerHTML;

   Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));

   // Tambahkan children jika ada
   if (Array.isArray(children) && children.length > 0) {
     children.forEach(child => {
       if (child instanceof Node) {
         element.appendChild(child);
       } else if (typeof child === 'string' || typeof child === 'number') {
         element.appendChild(document.createTextNode(child));
       }
     });
   }

   return element;
 };


function createNavLink(config) {
    const defaultConfig = {
        label: '',
        href: '#',
        links: [],
        buttons: [],
        buttonGroups: [],
        icon: '',
        isDropdown: false,
        isButtonDropdown: false,
        linkClass: 'nav-link',
        dropdownClass: 'nav-item dropdown',
        menuClass: 'dropdown-menu',
        dropdownPosition: 'right',
        dropdownSize: 'sm',
        itemClass: 'dropdown-item',
        navItemClass: 'nav-item',
        buttonClass: 'btn btn-outline-secondary',
        buttonGroupClass: 'btn-group',
        attributes: {}
    };

    const settings = { ...defaultConfig, ...config };

    // Helper untuk membuat elemen ikon
    function createIcon(iconClass) {
        return iconClass ? createElement('i', `${iconClass} mr-1`) : null;
    }

    if (settings.isDropdown && settings.links.length > 0) {
        // Dropdown menu
        const dropdownNavItem = createElement('li', settings.dropdownClass);
        const dropdownToggle = createElement('a', `${settings.linkClass} dropdown-toggle`, settings.label, {
            href: settings.href,
            role: 'button',
            'data-toggle': 'dropdown',
            'aria-haspopup': 'true',
            'aria-expanded': 'false',
            ...settings.attributes
        });

        if (settings.icon) dropdownToggle.prepend(createIcon(settings.icon));

        const dropdownMenu = createElement('div', settings.menuClass + ' dropdown-menu-' + settings.dropdownPosition + ' dropdown-menu-' + settings.dropdownSize);
        settings.links.forEach(link => {
            const menuItem = createElement('a', settings.itemClass, link.label, { href: link.href || '#' });

            if (link.icon) menuItem.prepend(createIcon(link.icon));
            if (link.attributes) {
                Object.entries(link.attributes).forEach(([key, value]) => menuItem.setAttribute(key, value));
            }
            if (link.callback) {
                menuItem.addEventListener('click', event => {
                    event.preventDefault();
                    link.callback(link, event);
                });
            }
            dropdownMenu.appendChild(menuItem);
        });

        dropdownNavItem.appendChild(dropdownToggle);
        dropdownNavItem.appendChild(dropdownMenu);
        return dropdownNavItem;

    } else if (settings.buttons.length > 0) {
        // Tombol tunggal atau dropdown tombol
        const fragment = document.createDocumentFragment();
        settings.buttons.forEach(button => {
            if (settings.isButtonDropdown && button.links && button.links.length > 0) {
                // Dropdown tombol
                const buttonDropdown = createElement('div', `${settings.buttonGroupClass} dropdown`);
                const buttonToggle = createElement('button', `${settings.buttonClass} dropdown-toggle`, button.label, {
                    type: 'button',
                    'data-toggle': 'dropdown',
                    'aria-haspopup': 'true',
                    'aria-expanded': 'false',
                    ...button.attributes
                });

                if (button.icon) buttonToggle.prepend(createIcon(button.icon));
                const dropdownMenu = createElement('div', settings.menuClass + ' dropdown-menu-' + settings.dropdownPosition + ' dropdown-menu-' + settings.dropdownSize);

                button.links.forEach(link => {
                    const menuItem = createElement('a', settings.itemClass, link.label, { href: link.href || '#' });

                    if (link.icon) menuItem.prepend(createIcon(link.icon));
                    if (link.callback) menuItem.addEventListener('click', event => {
                        event.preventDefault();
                        link.callback(link, event);
                    });
                    dropdownMenu.appendChild(menuItem);
                });

                buttonDropdown.appendChild(buttonToggle);
                buttonDropdown.appendChild(dropdownMenu);
                const navItem = createElement('li', settings.navItemClass);
                navItem.appendChild(buttonDropdown);
                fragment.appendChild(navItem);

            } else {
                // Tombol tunggal
                const buttonElement = createElement('button', settings.buttonClass, button.label, {
                    type: 'button',
                    ...button.attributes
                });

                if (button.icon) buttonElement.prepend(createIcon(button.icon));
                if (button.onClick) buttonElement.addEventListener('click', button.onClick);

                const navItem = createElement('li', settings.navItemClass);
                navItem.appendChild(buttonElement);
                fragment.appendChild(navItem);
            }
        });
        return fragment;

    } else if (settings.buttonGroups.length > 0) {
        // Grup tombol
        const fragment = document.createDocumentFragment();
        settings.buttonGroups.forEach(group => {
            const buttonGroup = createElement('div', settings.buttonGroupClass);
            group.buttons.forEach(button => {
                const buttonElement = createElement('button', `${settings.buttonClass} ${button.class || ''}`, button.label, {
                    type: 'button',
                    ...button.attributes
                });

                if (button.icon) buttonElement.prepend(createIcon(button.icon));
                if (button.onClick) buttonElement.addEventListener('click', button.onClick);

                if(!button.isDropdown) buttonGroup.appendChild(buttonElement);

                // Tambahkan dropdown untuk tombol jika ada
                if (button.isDropdown && button.links && button.links.length > 0) {
                    const dropdownToggle = createElement('button', `${settings.buttonClass} dropdown-toggle`, button.label, {
                        type: 'button',
                        'data-toggle': 'dropdown',
                        'aria-haspopup': 'true',
                        'aria-expanded': 'false',
                    });
                    if (button.icon) dropdownToggle.prepend(createIcon(button.icon));

                    const dropdownMenu = createElement('div', settings.menuClass + ' dropdown-menu-' + settings.dropdownPosition + ' dropdown-menu-' + settings.dropdownSize);
                    button.links.forEach(link => {
                        const menuItem = createElement('a', settings.itemClass, link.label, { href: link.href || '#' });

                        if (link.icon) menuItem.prepend(createIcon(link.icon));
                        if (link.callback) menuItem.addEventListener('click', event => {
                            event.preventDefault();
                            link.callback(link, event);
                        });
                        dropdownMenu.appendChild(menuItem);
                    });

                    buttonGroup.appendChild(dropdownToggle);
                    buttonGroup.appendChild(dropdownMenu);
                }
            });
            const navItem = createElement('li', settings.navItemClass);
            navItem.appendChild(buttonGroup);
            fragment.appendChild(navItem);
        });
        return fragment;

    } else if (settings.links.length > 0) {
        // Banyak tautan
        const fragment = document.createDocumentFragment();
        settings.links.forEach(link => {
            const navItem = createElement('li', settings.navItemClass);
            const linkElement = createElement('a', settings.linkClass, link.label, { href: link.href || '#' });

            if (link.icon) linkElement.prepend(createIcon(link.icon));
            if (link.attributes) {
                Object.entries(link.attributes).forEach(([key, value]) => linkElement.setAttribute(key, value));
            }
            if (link.callback) {
                linkElement.addEventListener('click', event => {
                    event.preventDefault();
                    link.callback(link, event);
                });
            }

            navItem.appendChild(linkElement);
            fragment.appendChild(navItem);
        });
        return fragment;

    } else {
        // Tautan tunggal
        const navItem = createElement('li', settings.navItemClass);
        const singleLink = createElement('a', settings.linkClass, settings.label, { href: settings.href });

        if (settings.icon) singleLink.prepend(createIcon(settings.icon));
        if (settings.callback) {
            singleLink.addEventListener('click', event => {
                event.preventDefault();
                settings.callback(settings, event);
            });
        }

        navItem.appendChild(singleLink);
        return navItem;
    }
}

function createModal(config) {
    const defaultConfig = {
        id: 'modal',
        title: 'Modal Title',
        body: 'This is the modal body.',
        footer: [],
        backdrop: true,
        keyboard: true,
        modalClass: 'modal fade',
        dialogClass: 'modal-dialog',
        contentClass: 'modal-content',
        headerClass: 'modal-header',
        bodyClass: 'modal-body',
        footerClass: 'modal-footer',
        loading: false,
        loadingText: 'Loading...',
        onLoad: null,
    };

    const settings = { ...defaultConfig, ...config };

    const modal = createElement('div', settings.modalClass, '', { id: settings.id, tabindex: '-1', role: 'dialog' });
    const dialog = createElement('div', settings.dialogClass);
    const content = createElement('div', settings.contentClass);

    const header = createElement('div', settings.headerClass);
    const title = createElement('h5', '', settings.title);
    const closeButton = createElement('button', 'close', '×', {
        type: 'button',
        'data-dismiss': 'modal',
        'aria-label': 'Close'
    });
    header.appendChild(title);
    header.appendChild(closeButton);

    const body = createElement('div', settings.bodyClass);

    // Jika loading diaktifkan, tambahkan elemen loading
    if (settings.loading) {
        const loadingIndicator = createElement('div', 'text-center', settings.loadingText);
        body.appendChild(loadingIndicator);
    } else {
        body.innerHTML = settings.body; // Mengisi konten modal jika tidak loading
    }

    const footer = createElement('div', settings.footerClass);
    settings.footer.forEach(button => {
        const footerButton = createElement('button', '', button.label, {
            type: 'button',
            class: button.class || 'btn btn-primary',
            'data-dismiss': button.dismiss ? 'modal' : undefined,
        });
        if (button.callback) {
            footerButton.addEventListener('click', (event) => {
                event.preventDefault();
                button.callback(event);
            });
        }
        footer.appendChild(footerButton);
    });

    content.appendChild(header);
    content.appendChild(body);
    content.appendChild(footer);
    dialog.appendChild(content);
    modal.appendChild(dialog);

    // Tambahkan event listener untuk reset konten modal saat ditutup
    $(modal).on('hidden.bs.modal', () => {
        // Bersihkan konten modal setiap kali ditutup
        body.innerHTML = '';
    });

    // Menambahkan event listener untuk callback onLoad saat modal ditampilkan
    $(modal).on('shown.bs.modal', () => {
        // Reset konten modal dan memanggil onLoad
        if (settings.onLoad) {
            // Panggil fungsi onLoad untuk memuat konten
            settings.onLoad(body);
        }
    });

    return modal;
}

/**
 * Helper function to create an AdminLTE 3 card using createElement.
 * @param {string} title - The title of the card.
 * @param {string} bodyContent - The main content of the card body.
 * @param {string} icon - (Optional) Font Awesome icon for the card title.
 * @param {string} cardClass - (Optional) Additional classes for card styling.
 * @param {boolean} isCollapsed - (Optional) Indicates if the card starts collapsed.
 * @param {Array} tools - (Optional) Array of tools for the card header (e.g., buttons).
 * @returns {HTMLElement} - The created card element.
 */
 const createAdminCard = ({
     title,
     bodyContent,
     icon = '',
     id = '',
     cardClass = 'card-default',
     isCollapsed = false,
     usePills = true,
     tools = []
 }) => {
     // Create the card element
     const card = createElement('div', `card ${cardClass} ${isCollapsed ? 'collapsed-card' : ''}`,'', { id: id });

     // Create the card header
     const cardHeader = usePills ? createElement('div', 'card-header p-0') : createElement('div', 'card-header');

     // Create the card title
     if (title) {
         const cardTitle = createElement('h3', 'card-title');

         // Create icon element if present
         if (icon) {
             const iconElement = createElement('i', `fas ${icon} mr-2`);
             cardTitle.appendChild(iconElement); // Append the icon
         }

         const titleText = document.createTextNode(title); // Create a text node for the title
         cardTitle.appendChild(titleText); // Append the title text to the card title

         cardHeader.appendChild(cardTitle); // Append title to header
     }

     // Create the card tools
     const cardTools = usePills ? createElement('div', 'card-tools p-3') : createElement('div', 'card-tools');
     tools.forEach(tool => {
         const toolButton = createElement('button', 'btn btn-tool', '', {
             'data-card-widget': tool.widget
         });
         // Tambahkan callback onClick jika tersedia
         if (typeof tool.onClick === 'function') {
             toolButton.addEventListener('click', tool.onClick);
         }
         const iconElement = createElement('i', `fas ${tool.icon}`);
         toolButton.appendChild(iconElement);
         cardTools.appendChild(toolButton);
     });

     // Append tools to header
     cardHeader.appendChild(cardTools);

     // Create the card body
     const cardBody = createElement('div', 'card-body', bodyContent, {'id': `${id}-content`});

     // Append header and body to the card
     card.appendChild(cardHeader);
     if(bodyContent !== false) card.appendChild(cardBody);

     return card;
 };



// Helper Functions
/**
 * Updates the displayed count for the specified data type.
 * @param {Object} counts - An object containing the count for the specified data type.
 * @param {number} counts[dataType] - The count of files for the specific data type.
 */
 function updateCountDisplay(counts) {
     // Validate input type
     if (typeof counts !== 'object' || counts === null) {
         ConsoleManager.error('Invalid input: counts must be an object.');
         return;
     }

     // Iterate through all data types in the counts object
     Object.keys(counts).forEach(dataType => {
         const count = counts[dataType] !== undefined ? counts[dataType] : 0; // Default to 0 if no data

         // Validate count
         if (typeof count !== 'number') {
             ConsoleManager.error(`Invalid input: Count for ${dataType} must be a number.`);
             return;
         }

         // Select the count display element for this data type
         const countDisplay = document.getElementById(`count${capitalize(dataType)}Files`);

         // Ensure display element exists
         if (!countDisplay) {
             ConsoleManager.error(`Display element with ID count${capitalize(dataType)}Files not found in the DOM.`);
             return;
         }

         // Update display value with formatting
         countDisplay.textContent = count.toLocaleString(); // Format with thousands separator
     });
 }


 // Helper function to create a select element with options and additional configurations
 const createSelect = ({
     id,
     name = '',
     attributes = {},
     optionsList = [],
     selectedValue = '',
     isDisabled = false,
     defaultOption = null
 }) => {
     const selectElement = createElement('select', 'form-control', '', {
         id,
         name,
         style: 'width: 100%;',
         ...attributes
         //disabled: isDisabled ? 'disabled' : undefined
     });

     // Set the disabled attribute conditionally
     if (isDisabled) {
         selectElement.setAttribute('disabled', 'disabled'); // Set disabled attribute
     }

     // Add default option if provided
     if (defaultOption) {
         const defaultOptionElement = createElement('option', '', defaultOption.label, { value: defaultOption.value });
         selectElement.appendChild(defaultOptionElement);
     }

     // Populate options
     optionsList.forEach(item => {
         const optionElement = createElement('option', '', item.label, { value: item.value });
         if (item.value === selectedValue) {
             optionElement.selected = true;
         }
         selectElement.appendChild(optionElement);
     });

     return selectElement;
 };


 const updateSelectOptions = (selectElement, items, { useSavedValue, sessionKey, defaultOption }) => {
     selectElement.innerHTML = ''; // Clear existing options

     // Add the default option if it's defined
     if (defaultOption) {
         const defaultOptionElement = createElement('option', '', defaultOption.label, { value: defaultOption.value });
         selectElement.appendChild(defaultOptionElement);
     }

     const savedValue = useSavedValue ? Session.get(sessionKey) : null;
     let isSelected = false; // Flag to check if an option has been selected

     items.forEach((item, index) => {
         const optionElement = createElement('option', '', item.label, { value: item.value });

         // If no saved value and this is the first option, set it as default in session if no defaultOption exists
         if (!savedValue && useSavedValue && index === 0 && !defaultOption) {
             Session.set(sessionKey, item.value);
             optionElement.selected = true; // Set the first item as selected
             isSelected = true;
         }

         // If saved value exists and matches the current item, select it
         if (useSavedValue && item.value === savedValue) {
             optionElement.selected = true;
             isSelected = true; // Mark that a value has been selected
         }

         selectElement.appendChild(optionElement);
     });

     // If no saved value and no item was selected, select the first option (either default or the first in the list)
     if (!isSelected) {
         if (defaultOption) {
             selectElement.value = defaultOption.value;
         } else {
             selectElement.selectedIndex = 0; // Select the first item in the list
         }
     }
 }


 // Main function to manage the select element with session management and options fetch
 const createSelectWithSession = (selectId, items, options = {}) => {
     const {
         name = '',
         sessionKey = `select-${selectId}`,
         autoChange = false,
         useSavedValue = true,
         serverSide = false,
         fetchOptions = null,
         defaultOption = null,
         helpText = '',
         feedback = '',
         isInvalid = false,
         label = '',
         onChange = null,
         isDisabled = false,
     } = options;

     // Get or create the select element
     let selectElement = document.getElementById(selectId) || createSelect({
         id: selectId,
         name,
         isDisabled,
         optionsList: items,
         selectedValue: useSavedValue ? Session.get(sessionKey) : defaultOption?.value || '',
         defaultOption
     });

     // Wrapper for select element
     const wrapperElement = createElement('div', 'form-group');

     // Append label if provided
     if (label) {
         const labelElement = createElement('label', 'col-form-label', label, { for: selectId });
         wrapperElement.appendChild(labelElement);
     }

     // Append select to wrapper
     wrapperElement.appendChild(selectElement);

     // Add help text if provided
     if (helpText) {
         const helpTextElement = createElement('small', 'form-text text-muted', helpText);
         wrapperElement.appendChild(helpTextElement);
     }

     // Add feedback if provided
     if (feedback) {
         const feedbackElement = createElement('div', isInvalid ? 'invalid-feedback' : 'valid-feedback', feedback);
         wrapperElement.appendChild(feedbackElement);
     }

     // Function to fetch and update options
     const fetchAndPopulateOptions = () => {
         if (serverSide && typeof fetchOptions === 'function') {
             fetchOptions()
                 .then(fetchedItems => {
                     updateSelectOptions(selectElement, fetchedItems, { useSavedValue, sessionKey, defaultOption });
                     if (autoChange) {
                         triggerAutoChange(Session.get(sessionKey) || defaultOption?.value);
                     }
                 })
                 .catch(err => ConsoleManager.error('Error fetching options:', err));
         } else {
             updateSelectOptions(selectElement, items, { useSavedValue, sessionKey, defaultOption });
             if (autoChange) {
                 triggerAutoChange(Session.get(sessionKey) || defaultOption?.value);
             }
         }
     };

     // Function to trigger change event programmatically
     const triggerAutoChange = (value) => {
         if (selectElement.querySelector(`option[value="${value}"]`)) {
             selectElement.value = value;
             //selectElement.dispatchEvent(new Event('change', { bubbles: true, detail: { programmatic: true } })); // Change to standard event
             selectElement.dispatchEvent(new CustomEvent('change', { detail: { programmatic: true } }));
         }
     };

     // Handle select change event
     const handleChange = (event) => {
         const selectedValue = event.target.value;
         Session.set(sessionKey, selectedValue);
         const isProgrammaticChange = event.detail?.programmatic === true;
         if (onChange) {
             onChange(selectedValue, isProgrammaticChange);
         }
     };

     // Add change event listener
     selectElement.addEventListener('change', handleChange);

     // Fetch options if needed
     fetchAndPopulateOptions();

     // Trigger auto-change if necessary
     if (autoChange) {
         const initialValue = Session.get(sessionKey) || defaultOption?.value;
         triggerAutoChange(initialValue);
     }

     // Return the wrapper element
     return {
         wrapperElement,
         selectElement,
         onChange: (callback) => {
             selectElement.addEventListener('change', event => callback(event.target.value));
         }
     };
 };


 const createButton = (options = {}) => {
     const {
         type = 'button',          // Default type untuk tombol
         id,
         text = 'Submit',          // Default text pada tombol
         classNames = 'btn-primary', // Default class untuk tombol (menggunakan AdminLTE 3)
         isDisabled = false,       // Tombol disabled atau tidak
         attributes = {},          // Atribut tambahan untuk tombol
         onClick = null,           // Callback saat tombol diklik
         isBlock = false,          // Tombol full-width (block button)
         icon = '',                // Icon (misalnya: 'fa fa-save')
     } = options;

     // Membuat elemen button
     const button = createElement('button', `btn ${classNames}`, '', {
         type,
         id,
         ...attributes
     });

     // Tambahkan icon jika ada
     if (icon) {
         const iconElement = createElement('i', icon);
         button.appendChild(iconElement);
         // Tambahkan spasi setelah icon
         button.appendChild(document.createTextNode(' '));
     }

     // Tambahkan teks tombol
     button.appendChild(document.createTextNode(text));

     // Set tombol menjadi block jika isBlock true
     if (isBlock) {
         button.classList.add('btn-block');
     }

     // Atur disabled jika isDisabled true
     if (isDisabled) {
         button.setAttribute('disabled', 'disabled');
     }

     // Tambahkan event listener untuk onClick jika ada
     if (onClick && typeof onClick === 'function') {
         button.addEventListener('click', onClick);
     }

     return button;
 };


 // Updated createInput helper with session support
 const createLabel = (options = {}) => {
     const {
         id,
         attributes = {},
         label = '', // Label for the input
         helpText = '', // Help text for the input
         feedback = '' // Feedback message
     } = options;

     // Create container div for label and input
     const container = createElement('div', 'form-group', '', { id: `wrapper${id}` });

     // Create label if provided
     if (label) {
         const labelElement = createElement('label', '', label, { for: id });
         container.appendChild(labelElement);
     }
     const content = createElement('div', 'form-group', '', { id: `content${id}` });
     container.appendChild(content);

     // Create feedback element if provided
     if (feedback) {
         const feedbackElement = createElement('div', 'invalid-feedback', feedback);
         container.appendChild(feedbackElement);
     }

     // Create help text if provided
     if (helpText) {
         const helpTextElement = createElement('small', 'form-text text-muted', helpText);
         container.appendChild(helpTextElement);
     }

     return container;
 };


 // Updated createInput helper with session support
 const createInput = (options = {}) => {
     const {
         type = 'text',
         id,
         name,
         value = '',
         placeholder = '',
         isDisabled = false,
         attributes = {},
         sessionKey, // Session key for storing value
         onChange = null,
         label = '', // Label for the input
         helpText = '', // Help text for the input
         feedback = '', // Feedback message
         isInvalid = false, // To indicate if the input is invalid
     } = options;

     // Create container div for label and input
     const container = createElement('div', 'form-group', '', { id: `wrapper${id}` });

     // Create label if provided
     if (label) {
         const labelElement = createElement('label', '', label, { for: id });
         container.appendChild(labelElement);
     }

     // Create input element
     const input = createElement('input', 'form-control', '', {
         type,
         id,
         name,
         placeholder,
         value: sessionKey ? Session.get(sessionKey, value) : value,
         ...attributes
     });

     // Set the disabled attribute conditionally
     if (isDisabled) {
         input.setAttribute('disabled', 'disabled'); // Set disabled attribute
     }

     // Add invalid class if the input is invalid
     if (isInvalid) {
         input.classList.add('is-invalid');
     }

     // Save value to session storage on input change
     input.addEventListener('input', (event) => {
         const newValue = event.target.value;
         if (sessionKey) {
             Session.set(sessionKey, newValue); // Store the value in session
         }
         if (onChange && typeof onChange === 'function') {
             onChange(newValue); // Call the onChange callback
         }
     });

     container.appendChild(input);

     // Create feedback element if provided
     if (feedback) {
         const feedbackElement = createElement('div', 'invalid-feedback', feedback);
         container.appendChild(feedbackElement);
     }

     // Create help text if provided
     if (helpText) {
         const helpTextElement = createElement('small', 'form-text text-muted', helpText);
         container.appendChild(helpTextElement);
     }

     return container;
 };


 const createTextarea = (options = {}) => {
     const {
         id,
         name,
         rows = 3,                        // Default number of rows
         placeholder = '',
         value = '',
         isDisabled = false,
         attributes = {},
         sessionKey,                      // Session key for storing value
         onChange = null,
         label = '',                      // Label for the textarea
         helpText = '',                   // Help text for the textarea
         feedback = '',                   // Feedback message
         isInvalid = false,               // To indicate if the textarea is invalid
     } = options;

     // Create container div for label and textarea
     const container = createElement('div', 'form-group', '', { id: `wrapper${id}` });

     // Create label if provided
     if (label) {
         const labelElement = createElement('label', '', label, { for: id });
         container.appendChild(labelElement);
     }

     // Create textarea element
     const textarea = createElement('textarea', 'form-control', '', {
         id,
         name,
         rows,
         placeholder,
         ...attributes // Spread other attributes
     });

     // Set the disabled attribute conditionally
     if (isDisabled) {
         textarea.setAttribute('disabled', 'disabled'); // Set disabled attribute
     }

     // Set the initial value from session storage if a session key is provided
     textarea.value = sessionKey ? Session.get(sessionKey, value) : value;

     // Add invalid class if the textarea is invalid
     if (isInvalid) {
         textarea.classList.add('is-invalid');
     }

     // Save value to session storage on input change
     textarea.addEventListener('input', (event) => {
         const newValue = event.target.value;
         if (sessionKey) {
             Session.set(sessionKey, newValue); // Store the value in session
         }
         if (onChange && typeof onChange === 'function') {
             onChange(newValue); // Call the onChange callback
         }
     });

     container.appendChild(textarea);

     // Create feedback element if provided
     if (feedback) {
         const feedbackElement = createElement('div', 'invalid-feedback', feedback);
         container.appendChild(feedbackElement);
     }

     // Create help text if provided
     if (helpText) {
         const helpTextElement = createElement('small', 'form-text text-muted', helpText);
         container.appendChild(helpTextElement);
     }

     return container;
 };

 const createCheckbox = ({
    label = '',
    id,
    name = '',
    value = '',
    isChecked = false,
    isDisabled = false,
    attributes = {},
    sessionKey = null,       // Key untuk menyimpan status di sesi
    onChange = null,
    autoChange = false,
    helpText = '',           // Teks bantuan opsional
} = {}) => {
    // Buat elemen checkbox
    const checkbox = createElement('input', 'form-check-input', '', {
        type: 'checkbox',
        id,
        name,
        value,
        ...attributes
    });

    // Ambil status checked dari sesi atau gunakan nilai default
    const storedChecked = sessionKey ? Session.get(sessionKey, isChecked.toString()) : isChecked;
    checkbox.checked = storedChecked === 'true';

    // Tetapkan atribut disabled jika diperlukan
    if (isDisabled) {
        checkbox.setAttribute('disabled', 'disabled');
    }

    // Function untuk memicu event perubahan secara programmatic
    const triggerAutoChange = (checked) => {
        checkbox.checked = checked;
        // Pastikan checkbox ada sebelum dispatch event
        setTimeout(() => {
          checkbox.dispatchEvent(new CustomEvent('change'));
        }, 0)
        //ConsoleManager.log(`Checkbox state changed to: ${checked}`);

    };

    // Handle perubahan checkbox
    const handleChange = (event) => {
        const checked = event.target.checked;

        // Simpan status checked di sesi jika sessionKey tersedia
        if (sessionKey) {
            Session.set(sessionKey, checked.toString());
        }

        // Jalankan callback onChange jika disediakan
        if (onChange) {
            onChange(checked);
        }
    };

    // Event listener untuk perubahan checkbox
    checkbox.addEventListener('change', (event) => {
        const checked = event.target.checked;
        const isProgrammaticChange = event.detail?.programmatic === true;

        // Simpan status checked di sesi jika sessionKey tersedia
        if (sessionKey) {
            Session.set(sessionKey, checked.toString());
        }

        // Jalankan callback onChange jika disediakan
        if (onChange) {
            onChange(checked, isProgrammaticChange);
        }
    });

    // Jika autoChange aktif, tetapkan perubahan otomatis dari sesi
    if (autoChange && sessionKey) {
        const sessionValue = Session.get(sessionKey);
        const sessionChecked = sessionValue === 'true';
        triggerAutoChange(sessionChecked);
    }

    // Buat elemen label untuk checkbox
    const labelElement = createElement('label', 'form-check-label', label, { for: id });

    // Buat wrapper untuk checkbox dan label
    const wrapper = createElement('div', 'form-check');
    wrapper.appendChild(checkbox);
    wrapper.appendChild(labelElement);

    // Tambahkan teks bantuan jika disediakan
    if (helpText) {
        const helpTextElement = createElement('small', 'form-text text-muted', helpText);
        wrapper.appendChild(helpTextElement);
    }

    // Return wrapper elemen yang mengandung checkbox dan label
    return wrapper;
};



 const createRadio = ({
     label = '',
     id,
     name = '',
     value = '',
     isChecked = false,
     isDisabled = false,
     attributes = {},
     sessionKey = null,       // Key untuk menyimpan status di sesi
     onChange = null,
     autoChange = false,
     helpText = '',           // Teks bantuan opsional
 } = {}) => {
     // Buat elemen radio
     const radio = createElement('input', 'form-check-input', '', {
         type: 'radio',
         id,
         name,
         value,
         ...attributes
     });

     // Ambil status checked dari sesi atau gunakan nilai default
     const storedChecked = sessionKey ? Session.get(sessionKey) : null;
     radio.checked = storedChecked !== null ? storedChecked === value : isChecked;

     // Tetapkan atribut disabled jika diperlukan
     if (isDisabled) {
         radio.setAttribute('disabled', 'disabled');
     }

     // Event listener untuk perubahan radio
     radio.addEventListener('change', (event) => {
         if (event.target.checked) {
             // Simpan status checked di sesi jika sessionKey tersedia
             if (sessionKey) {
                 Session.set(sessionKey, value);
             }

             // Jalankan callback onChange jika disediakan
             if (typeof onChange === 'function') {
                 onChange(value);
             }

             // Jika autoChange aktif, cetak perubahan ke konsol
             if (autoChange) {
                 ConsoleManager.log(`Radio button selected with value: ${value}`);
             }
         }
     });

     // Buat elemen label untuk radio
     const labelElement = createElement('label', 'form-check-label', label, { for: id });

     // Buat wrapper untuk radio dan label
     const wrapper = createElement('div', 'form-check');
     wrapper.appendChild(radio);
     wrapper.appendChild(labelElement);

     // Tambahkan teks bantuan jika disediakan
     if (helpText) {
         const helpTextElement = createElement('small', 'form-text text-muted', helpText);
         wrapper.appendChild(helpTextElement);
     }

     // Return wrapper elemen yang mengandung radio dan label
     return wrapper;
 };

 // Helper function to create a group of AdminLTE 3 styled checkboxes
 const createCheckboxGroup = (options = {}) => {
     const {
         id = '',
         checkboxes = [],                // Array of checkbox options
         groupClass = 'form-group',      // Class for the group wrapper
         onChange = null,                // Callback function when a checkbox value changes
         autoChange = false,             // Automatically trigger onChange when state changes
     } = options;

     // Create group wrapper
     const groupWrapper = createElement('div', groupClass, '', { id: id ? `wrapper${id}` : '' });

     checkboxes.forEach(checkbox => {
         const checkboxField = createCheckbox({
             label: checkbox.label,
             id: checkbox.id,
             name: checkbox.name,
             value: checkbox.value,
             isChecked: checkbox.isChecked,
             isDisabled: checkbox.isDisabled,
             attributes: checkbox.attributes,
             sessionKey: checkbox.sessionKey, // Session key for storing checked state
             helpText: checkbox.helpText,
             onChange: (checked) => {


                 if (onChange && typeof onChange === 'function') {

                     onChange(checkbox.name, checked);
                 }
             },
             autoChange
         });


         // Append the created checkbox to the group wrapper
         groupWrapper.appendChild(checkboxField);
     });

     return groupWrapper;
 };

 // Helper function to create a group of AdminLTE 3 styled radio buttons
 const createRadioGroup = (options = {}) => {
     const {
         radios = [],                   // Array of radio options
         groupClass = 'form-group',     // Class for the group wrapper
         onChange = null,               // Callback function when a radio value changes
         autoChange = false             // Automatically trigger onChange when state changes
     } = options;

     // Create group wrapper
     const groupWrapper = createElement('div', groupClass);

     radios.forEach(radio => {
         const radioField = createRadio({
             label: radio.label,
             id: radio.id,
             name: radio.name,
             value: radio.value,
             isChecked: radio.isChecked,
             isDisabled: radio.isDisabled,
             attributes: radio.attributes,
             sessionKey: radio.sessionKey, // Session key for storing checked state
             onChange: (selectedValue) => {
                 if (onChange && typeof onChange === 'function') {
                     onChange(radio.name, selectedValue);
                 }
             },
             autoChange
         });

         // Append the created radio to the group wrapper
         groupWrapper.appendChild(radioField);
     });

     return groupWrapper;
 };



// Helper function to create an info box or small box styled with AdminLTE and Bootstrap 4
const createInfoBox = (stat) => {
    // Create the main column container
    const col = createElement('div', `col-md-${stat.colSize || 4}`);

    // Set the background class, supporting both regular and gradient backgrounds
    const bgClass = stat.isGradient ? `bg-gradient-${stat.skin || 'info'}` : `bg-${stat.skin || 'info'}`;

    // Decide whether to create an info-box or small-box based on the stat type
    if (stat.boxType === 'small-box') {
        // Create the small box layout with optional gradient background
        const smallBox = createElement('div', `small-box ${bgClass}`, '', { id: stat.id });
        const inner = createElement('div', 'inner');
        const h3 = createElement('h3', '', stat.number || '0');
        const p = createElement('p', '', stat.title || 'New Info');
        const icon = createElement('div', 'icon', `<i class="${stat.icon || 'fas fa-info-circle'}"></i>`);

        // Assemble the inner content
        inner.append(h3, p);

        // Optional footer link
        const footer = createElement('a', 'small-box-footer', `More info <i class="fas fa-arrow-circle-right"></i>`, { href: stat.link || '#' });

        // Assemble the small box
        smallBox.append(inner, icon, footer);
        col.appendChild(smallBox);

    } else {
        // Create the default info-box layout if not small-box
        const boxClass = stat.showProgress ? `info-box ${bgClass}` : `info-box ${stat.class || ''}`;
        const infoBox = createElement('div', boxClass, '', { id: stat.id });

        // Set icon background class (AdminLTE uses bg-* for background colors)
        const iconClass = stat.iconBgClass ? `info-box-icon ${stat.iconBgClass}` : `info-box-icon ${bgClass}`;
        const iconStyle = stat.iconSize ? `font-size: ${stat.iconSize}` : ''; // Allow for custom icon sizing
        const iconElement = createElement('span', iconClass, stat.icon || '<i class="far fa-envelope"></i>', { style: iconStyle });

        // Create the content container
        const content = createElement('div', 'info-box-content');
        const text = createElement('span', `info-box-text ${stat.textColor || ''}`, stat.title || 'Info'); // Add option for custom text color
        const number = createElement('span', `info-box-number ${stat.numberColor || ''}`, formatNumber(stat.number) || '0'); // Number with custom color and formatting

        // Append text and number to the content container
        content.append(text, number);

        // If a progress bar is required, add it
        //if (stat.showProgress) {
            const progressDiv = createElement('div', 'progress');
            const progressBarClass = stat.progressBarClass ? `progress-bar ${stat.progressBarClass}` : stat.showProgress ? 'progress-bar' : `progress-bar ${stat.iconBgClass ? stat.iconBgClass : bgClass }`;
            const progressBar = createElement('div', progressBarClass, '', { style: `width: ${stat.progress || 0}%` });
            const progressDescription = createElement('span', 'progress-description', `${stat.progressDescription || '0%'}`);

            progressDiv.appendChild(progressBar);
            content.append(progressDiv, progressDescription);
        //}

        // Assemble the info box
        infoBox.append(iconElement, content);
        col.appendChild(infoBox);
    }

    return col;
};

// Helper function to create a loading spinner
const createLoadingContent = () => {
    return `
        <div class="loading-spinner d-flex justify-content-center align-items-center" style="height: 100px;">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `;
};

// Helper function to create empty content when no data is available
const createEmptyContent = (message = 'No data available') => {
    return `<div class="text-center">${message}</div>`;
};

const createEmptyContent2 = (message = 'No data available') => `
    <div class="not-found alert alert-danger text-center">${message}</div>
`;

// Helper function to format numbers with commas or other custom formatting
const formatNumber = (number) => {
    if (number === undefined || number === null) return '0';
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); // Adds commas to large numbers
};

// Capitalize the first letter of a string
const capitalize = (string) => {
    if (typeof string !== 'string') {
        ConsoleManager.error('Provided value is not a string:', string);
        return ''; // Mengembalikan string kosong jika input bukan string
    }
    return string.charAt(0).toUpperCase() + string.slice(1);
};


const createFolderPicker = (options = {}) => {
    const {
        id,
        name,
        value = '', // Default folder path
        isDisabled = false, // Disable option
        attributes = {}, // Additional attributes
        sessionKey, // Session key for storing folder path
        onChange = null, // Callback for change events
        label = '', // Label for the folder picker
        helpText = '', // Help text for the folder picker
        feedback = '', // Feedback message
        displayFullPath = false, // Configuration: true to show full path, false to show folder name
        isInvalid = false, // To indicate if the folder picker is invalid
        defaultDirectory = null // Default directory to open
    } = options;

    // Create container div for label and folder picker
    const container = createElement('div', 'form-group', '', { id: `wrapper${id}` });

    // Create label if provided
    if (label) {
        const labelElement = createElement('label', '', label, { for: id });
        container.appendChild(labelElement);
    }

    // Determine the initial value for the input based on sessionKey or defaultDirectory
    let initialValue = Session.get(sessionKey, null); // Get the session value

    if (!initialValue) {
        // Jika sessionKey tidak ada, cek apakah defaultDirectory ada
        if (defaultDirectory) {
            // Extract the folder name from the default directory path
            const folderName = defaultDirectory.split('/').pop(); // Mendapatkan nama folder
            initialValue = folderName; // Gunakan nama folder sebagai nilai awal
        } else {
            initialValue = value; // Jika tidak ada defaultDirectory, gunakan value
        }
    }

    // Create input element to display the selected folder path
    const input = createElement('input', 'form-control', '', {
        type: 'text',
        id,
        name,
        placeholder: 'Pilih folder...',
        value: initialValue,
        readonly: 'readonly',
        ...attributes
    });

    // Set the disabled attribute conditionally
    if (isDisabled) {
        input.setAttribute('disabled', 'disabled');
    }

    // Add invalid class if the folder picker is invalid
    if (isInvalid) {
        input.classList.add('is-invalid');
    }

    // Create button for picking the folder
    const button = createElement('button', 'btn btn-primary mt-2', 'Pilih Folder', { type: 'button' });

    // Event listener for button to open folder picker dialog
    button.addEventListener('click', async () => {
        try {
            // Show directory picker
            const folderHandle = await window.showDirectoryPicker();
            let folderPath = folderHandle.name; // Get the folder name

            // If displayFullPath is true, you need to track the full path in another way
            if (displayFullPath) {
                let fullPath = folderPath; // Initialize fullPath with the current folder name
                // Note: You may need a way to manage the full path. This could be done via a stack during folder navigation.
            }

            // Set the selected folder path to input
            input.value = folderPath;

            // Save folder path to session storage
            if (sessionKey) {
                Session.set(sessionKey, folderPath);
            }

            // Call the onChange callback if provided
            if (onChange && typeof onChange === 'function') {
                onChange(folderPath);
            }
        } catch (error) {
            ConsoleManager.error('Folder selection was cancelled or failed:', error);
        }
    });

    // Append input and button to the container
    container.appendChild(input);
    container.appendChild(button);

    // Create feedback element if provided
    if (feedback) {
        const feedbackElement = createElement('div', 'invalid-feedback', feedback);
        container.appendChild(feedbackElement);
    }

    // Create help text if provided
    if (helpText) {
        const helpTextElement = createElement('small', 'form-text text-muted', helpText);
        container.appendChild(helpTextElement);
    }

    return container;
};


// Helper function to create Bootstrap 4 tabs with session storage and animation
const createNestedBootstrap4TabsWithSession = (tabs, options = {}) => {
    const {
        useCard = false,
        usePills = false,
        autoClick = false,
        sessionKey = 'activeTab',
        animationClass = 'fade',
        nestedContainerClass = 'nested-tab-container',
        onTabClick = null,
        cardTitle = 'Tab Container',
        cardClass = 'card-primary card-outline card-outline-tabs',
        tools = [],
        isCollapsed = false
    } = options;

    const nav = createElement('ul', usePills ? 'nav nav-pills mr-auto p-2' : 'nav nav-tabs', '', { role: 'tablist' });
    const tabContent = createElement('div', 'tab-content', '');
    const activeTabs = getActiveTabs(sessionKey, tabs); // Get active main and nested tabs

    tabs.forEach((tab, index) => {
        const tabId = tab.id || `tab${index}`;
        const isActive = activeTabs.mainTab === tabId;

        // Create Tab Item
        const tabItem = createElement('li', 'nav-item', '', { id: `${tabId}-item` });
        const tabLink = createElement('a', `nav-link ${isActive ? 'active' : ''}`, tab.label, {
            href: `#${tabId}`,
            'data-toggle': usePills ? 'pill' : 'tab',
            role: 'tab',
            'aria-controls': tabId,
            'aria-selected': isActive.toString(),
            id: `${tabId}-tab`
        });

        tabItem.appendChild(tabLink);
        nav.appendChild(tabItem);

        // Create Tab Pane
        const tabPane = createElement('div', `tab-pane ${animationClass} ${isActive ? 'show active' : ''}`, '', {
            id: tabId,
            role: 'tabpanel',
            'aria-labelledby': `${tabId}-tab`
        });

        // Handle Nested Tabs
        if (tab.nestedTabs) {
            const nestedTabs = createNestedBootstrap4TabsWithSession(tab.nestedTabs, {
                useCard: false,
                sessionKey: `${sessionKey}-${tabId}`,
                animationClass,
                nestedContainerClass,
                onTabClick
            });
            tabPane.appendChild(nestedTabs);
        } else {
            tabPane.innerHTML = tab.content;
        }

        tabContent.appendChild(tabPane);

        // Event listener for tab click
        tabLink.addEventListener('click', () => {
            Session.set(sessionKey, tabId); // Update session storage with active tab
            if (onTabClick) {
                onTabClick(tab); // Call the callback with the clicked tab data
            }
        });
    });

    // Create Tab Container
    const tabContainer = useCard ? createAdminCard({
        title: cardTitle,
        bodyContent: '',
        cardClass,
        isCollapsed,
        tools
    }) : createElement('div', nestedContainerClass, '');

    if (useCard) {
        const cardHeader = tabContainer.querySelector('.card-header');
        cardHeader.appendChild(nav);
        const cardBody = tabContainer.querySelector('.card-body');
        cardBody.appendChild(tabContent);
    } else {
        tabContainer.appendChild(nav);
        tabContainer.appendChild(tabContent);
    }

    const waitForElement = (selector, callback) => {
        const element = document.querySelector(selector);
        if (element) {
            callback(element);
        } else {
            requestAnimationFrame(() => waitForElement(selector, callback));
        }
    };

    // Gunakan waitForElement untuk memeriksa elemen dan klik otomatis
    waitForElement(`#${activeTabs.mainTab}-tab`, (activeTabElement) => {
        if (autoClick) {
            activeTabElement.click();
        }
    });



    return tabContainer;
};

// Helper function to get active tabs
const getActiveTabs = (sessionKey, tabs) => {
    const activeTabs = {};

    // Retrieve the main active tab from session or by finding the active class
    const mainActiveTabId = Session.get(sessionKey) || (tabs.length > 0 ? tabs[0].id : null); // Set first tab as default if session is empty
    activeTabs.mainTab = mainActiveTabId;

    // Check if the main active tab has nested tabs
    const nestedContainer = document.querySelector(`#${mainActiveTabId} .nested-tab-container`);
    if (nestedContainer) {
        // Retrieve nested active tab from session or by finding the active class within nested container
        const nestedSessionKey = `${sessionKey}-${mainActiveTabId}`;
        const nestedActiveTabId = Session.get(nestedSessionKey) || nestedContainer.querySelector('.nav-link.active')?.getAttribute('href').substring(1);
        activeTabs.nestedTab = nestedActiveTabId;
    }

    return activeTabs;
};
