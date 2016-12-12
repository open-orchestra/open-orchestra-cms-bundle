import NodeRouter            from './Router/Node/NodeRouter'
import KeywordRouter         from './Router/Keyword/KeywordRouter'
import DashboardRouter       from './Router/Dashboard/DashboardRouter'
import SiteRouter            from './Router/Site/SiteRouter'
import ErrorView             from './View/Error/ErrorView'
import ApplicationError      from '../Service/Error/ApplicationError'
import AjaxError             from '../Service/Error/AjaxError'
import HeaderView            from './View/Header/HeaderView'
import SitesAvailable        from './Collection/Site/SitesAvailable'
import NavigationView        from './View/Navigation/NavigationView'
import FormBehaviorManager   from '../Service/Form/Behavior/Manager'
import ScrollTable           from '../Service/Form/Behavior/ScrollTable'
import Tooltip               from '../Service/Form/Behavior/Tooltip'
import TagSelect2            from '../Service/Form/Behavior/TagSelect2'
import DatePicker            from '../Service/Form/Behavior/DatePicker'
import NodeChoice            from '../Service/Form/Behavior/NodeChoice'
import NodeTemplateSelection from '../Service/Form/Behavior/NodeTemplateSelection'
import GenerateId            from '../Service/Form/Behavior/GenerateId'
import CollectionSortable    from '../Service/Form/Behavior/CollectionSortable'

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
        Backbone.Events.on('application:error', this._displayError, this);
    }

    /**
     * Run Application
     */
    run() {
        this._initRouting();
        this._initTranslator();
        this._initRouter();
        this._initFormBehaviorManager();

        Backbone.Events.trigger('application:before:start');

        this._initLayoutView();
        Backbone.history.start();

        Backbone.Events.trigger('application:after:start');
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
     * @param {Context} context - Context object
     */
    setContext(context) {
        this._context = context;
    }

    /**
     * @returns {Context}
     */
    getContext() {
        return this._context;
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
     * @param {Error} error
     * @private
     */
    _displayError(error) {
        if (error instanceof AjaxError && error.getStatusCode() === 401) {
            window.location.pathname = Routing.generate('fos_user_security_login', true);
        }
        if (this.getConfiguration().getParameter('debug')) {
            let errorView = new ErrorView({error: error});
            this.getRegion('content').html(errorView.render().$el);
        }
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new DashboardRouter();
        new NodeRouter();
        new KeywordRouter();
        new SiteRouter();
    }

    /**
     * Initialize layout view (header, nav, ...)
     * @private
     */
    _initLayoutView() {
        new SitesAvailable().fetch({
            success: (sites) => {
                let headerView = new HeaderView({sites : sites});
                this.getRegion('header').html(headerView.render().$el);
            }
        });
        let navigationView = new NavigationView();
        this.getRegion('left_column').html(navigationView.render().$el);
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
        Translator.locale = this.getContext().language;
        Translator.defaultDomain = 'interface';
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(NodeTemplateSelection);
        FormBehaviorManager.add(ScrollTable);
        FormBehaviorManager.add(Tooltip);
        FormBehaviorManager.add(TagSelect2);
        FormBehaviorManager.add(NodeChoice);
        FormBehaviorManager.add(DatePicker);
        FormBehaviorManager.add(GenerateId);
        FormBehaviorManager.add(CollectionSortable);
    }

}

// unique instance of Application
export default (new Application);
