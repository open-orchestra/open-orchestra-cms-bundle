/**
 * @class TinymceManager
 */
class TinymceManager
{
    /**
     * Constructor
     */
    constructor () {
        this._settings = this._getDefaultSettings();
        this._buttons = {};
    }

    /**
     * @param {object} $element
     *
     * @return Editor
     */
    createEditor($element) {
        this.removeEditor($element);

        tinymce.baseURL = '/tinymce';
        $element.tinymce(this._settings);

        return $element.tinymce();
    }

    /**
     * @param {object} $element
     */
    removeEditor($element) {
        let id = $element.attr('id');
        tinymce.EditorManager.remove('#'+id);
    }

    /**
     * @param {Object} settings
     */
    setSettings(settings) {
        this._settings = settings;
    }

    /**
     * Get a setting
     * @param {string} name
     *
     * @return {mixed}
     */
    getSetting(name) {
        return this._settings[name];
    }

    /**
     * Override a setting
     * @param {string} name
     * @param {mixed}  setting
     */
    setSetting(name, setting) {
        this._settings[name] = setting;
    }

    /**
     * remove a setting
     * @param {string} name
     */
    removeSetting(name) {
        delete this._settings[name];
    }

    /**
     * @param {string} plugin
     */
    activatePlugin(plugin) {
        this._settings['plugins'] = this._settings['plugins'].concat(plugin + ' ');
    }

    /**
     * @param {string}  name
     * @param {Object} settings
     */
    createButton(name, settings) {
        this._buttons[name] = settings;
    }

    /**
     * @returns {Object}
     */
    _getDefaultSettings() {
        return {
            plugins: 'advlist autolink lists image charmap print preview hr anchor pagebreak \
                      searchreplace wordcount visualblocks visualchars code fullscreen \
                      insertdatetime media nonbreaking save table contextmenu directionality \
                      emoticons template paste textcolor link \
                      orchestra_internal_link orchestra_bbcode',
            toolbar: 'undo redo | styleselect bold italic forecolor backcolor |  \
                      alignleft aligncenter alignright alignjustify | \
                      bullist numlist outdent indent | link internal_link',
            height: 200,
            menubar: false,
            contextmenu: 'link inserttable | cell row column deletetable',
            setup: (editor) => {
                $.each(this._buttons, (name, buttonSettings) => {
                    editor.addButton(name, buttonSettings);
                });
            }
        };
    }
}

// unique instance of Application
export default (new TinymceManager);
