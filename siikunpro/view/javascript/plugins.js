// Load plugins using fetch instead of $.ajax
const loadPlugins = async () => {

  // Load plugins
  const plugins = document.getElementById('plugins');
  let pluginLists = '';
  const pluginsUrl = 'index.php?route=obfuscator/plugins/lists';


    try {
        const response = await fetch(pluginsUrl, {
            method: 'POST',
            cache: 'no-cache'
        });
        const obj = await response.json();
        obj.forEach(v => {
            pluginLists += v.display;
        });

        if (pluginLists) {
            //plugins.innerHTML = pluginLists;
        }
    } catch (error) {
        //console.error('Error loading plugins:', error);
    }
};
