import NodeController from './Controller/Node/NodeController'
import ErrorController from './Controller/Error/ErrorController'
import TemplateManager from '../Service/TemplateManager'
import ApplicationError from './Error/ApplicationError'

/**
 * @class Application
 */
class Application
{
    /**
     * Constructor
     */
    constructor() {
        this._regions = {};
        window.addEventListener('error', this._applicationError);
    }

    /**
     * Run Application
     */
    run() {

        this._initRouting();
        this._initTranslator();
        this._initTemplateManager();
        this._initController();

        if (Routing.generate('fos_user_security_login', true) != document.location.pathname) {
            Backbone.Events.trigger('application:before:start');
            Backbone.history.start();
            Backbone.Events.trigger('application:after:start');
        }
    }

    /**
     * @param {string} name
     * @param {Object} $selector - Jquery selector
     */
    addRegion(name, $selector) {
        this._regions[name] = $selector;
    }

    /**
     * @param {Object} regions
     */
    setRegions(regions) {
        this._regions = regions;
    }

    /**
     * @param {string} name
     */
    getRegion(name) {
        return this._regions[name];
    }

    /**
     * set Application configuration
     * @param {Configuration} configuration - Configuration object
     */
    setConfiguration(configuration) {
        this._configuration = configuration;
    }

    /**
     * get Application configuration
     *
     * @returns {Configuration}
     */
    getConfiguration() {
        return this._configuration;
    }

    /**
     * @param {Object} err - ErrorEvent
     * @private
     */
    _applicationError(err) {
        let error = new ApplicationError(err.message);
        Backbone.Events.trigger('application:error', error);
    }

    /**
     * Initialize controller
     * @private
     */
    _initController() {
        new ErrorController();
        new NodeController();
    }

    /**
     * Initialize template manager
     * @private
     */
    _initTemplateManager() {
        TemplateManager.initialize(
            this._configuration.getParameter('template'),
            this._configuration.getParameter('environment')
        );
    }

    /**
     * Initialize routing
     * @private
     */
    _initRouting() {
        let routingConfiguration = this._configuration.getParameter('routing');
        fos.Router.setData({
            'base_url' : routingConfiguration.baseUrl,
            'scheme' : routingConfiguration.scheme,
            'host' : routingConfiguration.host,
            'routes': Routing.getRoutes()
        });

    }

    /**
     * Initialize translator
     * @private
     */
    _initTranslator() {
        Translator.locale = this._configuration.getParameter('language');
    }
}

// unique instance of Application
export default (new Application);
