import ServerError from './Error/ServerError'

/**
 * @class TemplateManager
 */
class TemplateManager
{
    /**
     * constructor
     */
    constructor() {
        this._templates = {};
        this._promises = {};
    }

    /**
     * @param {string} baseUrlTemplate
     * @param {string} templateVersion
     * @param {string} environment
     */
    initialize({baseUrlTemplate, templateVersion}, environment) {
        this._baseUrlTemplate = baseUrlTemplate;
        this._templateVersion = templateVersion;
        this._localStorageKey = 'template-underscore-' + environment;
        this._loadTemplatesLocalStorage();
    }

    /**
     * Clear template in cache
     */
    clearCache() {
        this._templates = {};
        if (typeof localStorage!='undefined') {
            localStorage.removeItem(this._localStorageKey)
        }
    }

    /**
     * get template
     *
     * @param {string} name
     * @param {function} callback
     */
    get(name, callback) {
        let templateManager = this;
        let key = this._getTemplateKeyStorage(name);

        if (this._templates.hasOwnProperty(key)) {
            let compiledTemplate = _.template(this._templates[key]);
            callback(compiledTemplate);
        } else {
            this._loadTemplate(name).then(
                (template) => {
                    let compiledTemplate = _.template(template);
                    templateManager._templates[key] = template;
                    templateManager._storeTemplateLocalStorage();

                    callback(compiledTemplate);
                },
                (response) => {
                    let error =new ServerError(response.status, response.responseText, response.statusText);
                    Backbone.Events.trigger('application:error', error);
                }
            );
        }
    }

    /**
     * Load template on the server
     *
     * @param {string} name
     * @private
     */
    _loadTemplate(name) {
        let url = this._baseUrlTemplate + name + '._tpl.html';
        let key = this._getTemplateKeyStorage(name);
        let promise = this._promises[key] ||  new Promise((resolve, reject) => {
                Backbone.ajax({method: 'GET',url: url}).done(resolve).fail(reject);
        });
        this._promises[key] = promise;

        return promise
    }

    /**
     * Load template underscore in local storage
     * @private
     */
    _loadTemplatesLocalStorage() {
        if (typeof localStorage !== 'undefined') {
            if (localStorage.hasOwnProperty(this._localStorageKey)) {
                this._templates = JSON.parse(localStorage.getItem(this._localStorageKey));
            }
        }
    }

    /**
     * Store template underscore in local storage
     * @private
     */
    _storeTemplateLocalStorage() {
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem(this._localStorageKey, JSON.stringify(this._templates));
        }
    }

    /**
     * @param name
     *
     * @returns {string}
     * @private
     */
    _getTemplateKeyStorage(name) {
        return name + '?' + this._templateVersion;
    }
}

export default (new TemplateManager)
