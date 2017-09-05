import NodeRouter              from 'OpenOrchestra/Application/Router/Node/NodeRouter'
import KeywordRouter           from 'OpenOrchestra/Application/Router/Keyword/KeywordRouter'
import DashboardRouter         from 'OpenOrchestra/Application/Router/Dashboard/DashboardRouter'
import SiteRouter              from 'OpenOrchestra/Application/Router/Site/SiteRouter'
import SitePlatformRouter      from 'OpenOrchestra/Application/Router/Site/SitePlatformRouter'
import ContentTypeRouter       from 'OpenOrchestra/Application/Router/ContentType/ContentTypeRouter'
import ContentRouter           from 'OpenOrchestra/Application/Router/Content/ContentRouter'
import BlockRouter             from 'OpenOrchestra/Application/Router/Block/BlockRouter'
import SharedBlockRouter       from 'OpenOrchestra/Application/Router/Block/SharedBlockRouter'
import RedirectionRouter       from 'OpenOrchestra/Application/Router/Redirection/RedirectionRouter'
import TrashItemRouter         from 'OpenOrchestra/Application/Router/TrashItem/TrashItemRouter'

import HeaderView              from 'OpenOrchestra/Application/View/Header/HeaderView'
import ErrorView               from 'OpenOrchestra/Application/View/Error/ErrorView'
import NavigationManager       from 'OpenOrchestra/Service/NavigationManager'
import MenuView                from 'OpenOrchestra/Application/View/Menu/MenuView'
import BreadcrumbView          from 'OpenOrchestra/Application/View/Breadcrumb/BreadcrumbView'
import NodeRestoreModalView    from 'OpenOrchestra/Application/View/TrashItem/NodeRestoreModalView'
import ContentRestoreModalView from 'OpenOrchestra/Application/View/TrashItem/ContentRestoreModalView'

import SitesAvailable          from 'OpenOrchestra/Application/Collection/Site/SitesAvailable'

import FormBehaviorManager     from 'OpenOrchestra/Service/Form/Behavior/Manager'
import PatchSubmit             from 'OpenOrchestra/Service/Form/Behavior/PatchSubmit'
import PatchAndSendSubmit      from 'OpenOrchestra/Service/Form/Behavior/PatchAndSendSubmit'
import ScrollTable             from 'OpenOrchestra/Service/Form/Behavior/ScrollTable'
import Tooltip                 from 'OpenOrchestra/Service/Form/Behavior/Tooltip'
import TagSelect2              from 'OpenOrchestra/Service/Form/Behavior/TagSelect2'
import DatePicker              from 'OpenOrchestra/Service/Form/Behavior/DatePicker'
import TreeChoice              from 'OpenOrchestra/Service/Form/Behavior/TreeChoice'
import NodeTemplateSelection   from 'OpenOrchestra/Service/Form/Behavior/NodeTemplateSelection'
import GenerateId              from 'OpenOrchestra/Service/Form/Behavior/GenerateId'
import CollectionSortable      from 'OpenOrchestra/Service/Form/Behavior/CollectionSortable'
import Accordion               from 'OpenOrchestra/Service/Form/Behavior/Accordion'
import BlockVideoType          from 'OpenOrchestra/Service/Form/Behavior/BlockVideoType'
import ColorPicker             from 'OpenOrchestra/Service/Form/Behavior/ColorPicker'
import Tinymce                 from 'OpenOrchestra/Service/Form/Behavior/Tinymce'
import TagCondition            from 'OpenOrchestra/Service/Form/Behavior/TagCondition'
import ContentType             from 'OpenOrchestra/Service/Form/Behavior/ContentType'
import InputFile               from 'OpenOrchestra/Service/Form/Behavior/InputFile'

import SearchFormGroupManager  from 'OpenOrchestra/Service/SearchFormGroup/Manager'
import DateSearchFormGroup     from 'OpenOrchestra/Service/SearchFormGroup/DateForm'
import TextSearchFormGroup     from 'OpenOrchestra/Service/SearchFormGroup/TextForm'
import NumberSearchFormGroup   from 'OpenOrchestra/Service/SearchFormGroup/NumberForm'

import CellFormatterManager    from 'OpenOrchestra/Service/DataFormatter/Manager'
import TextFormatter           from 'OpenOrchestra/Service/DataFormatter/TextFormatter'
import BooleanFormatter        from 'OpenOrchestra/Service/DataFormatter/BooleanFormatter'
import DateFormatter           from 'OpenOrchestra/Service/DataFormatter/DateFormatter'
import StatusFormatter         from 'OpenOrchestra/Service/DataFormatter/StatusFormatter'
import SiteFormatter           from 'OpenOrchestra/Service/DataFormatter/SiteFormatter'

import ApplicationError        from 'OpenOrchestra/Service/Error/ApplicationError'
import TinymceManager          from 'OpenOrchestra/Service/Tinymce/TinymceManager'

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
        this._initConfiguration();
        this._initRouting();
        this._initTranslator();
        this._initRouter();
        this._initFormBehaviorManager();
        this._initSearchFormGroupManager();
        this._initCellFormatterManager();

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
        if (('ApiError' === error.name || 'ServerError' === error.name) && error.statusCode === 401) {
            window.location.pathname = Routing.generate('fos_user_security_login', true);
        } else {
            let type = ('ApiError' === error.name) ? 'warning' : 'danger';
            let errorView = new ErrorView({error: error, type: type});
            this.getRegion('modal').html(errorView.render().$el);
            errorView.show();
        }
    }

    _initConfiguration() {
        this.getConfiguration().addParameter('restoreModalViews', {
            'node':    NodeRestoreModalView,
            'content': ContentRestoreModalView
        });
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
        new SitePlatformRouter();
        new ContentTypeRouter();
        new ContentRouter();
        new BlockRouter();
        new SharedBlockRouter();
        new RedirectionRouter();
        new TrashItemRouter();
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
        let menuView = new MenuView();
        this.getRegion('left_column').html(menuView.render().$el);

        let breadcrumbView = new BreadcrumbView();
        this.getRegion('breadcrumb').html(breadcrumbView.render().$el);

        NavigationManager.initialize(menuView, breadcrumbView);
    }

    /**
     * Initialize routing
     * @private
     */
    _initRouting() {
        let routingConfiguration = this.getContext().get('routing');
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
        Translator.locale = this.getContext().get('language');
        Translator.defaultDomain = 'interface';
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(PatchSubmit);
        FormBehaviorManager.add(PatchAndSendSubmit);
        FormBehaviorManager.add(NodeTemplateSelection);
        FormBehaviorManager.add(ScrollTable);
        FormBehaviorManager.add(Tooltip);
        FormBehaviorManager.add(TagSelect2);
        FormBehaviorManager.add(TreeChoice);
        FormBehaviorManager.add(DatePicker);
        FormBehaviorManager.add(GenerateId);
        FormBehaviorManager.add(CollectionSortable);
        FormBehaviorManager.add(Accordion);
        FormBehaviorManager.add(BlockVideoType);
        FormBehaviorManager.add(ColorPicker);
        FormBehaviorManager.add(TagCondition);
        FormBehaviorManager.add(ContentType);
        FormBehaviorManager.add(InputFile);

        TinymceManager.setSetting('language', this.getContext().get('language'));
        FormBehaviorManager.add(Tinymce);
    }

    /**
     * Initialize field search library
     * @private
     */
    _initSearchFormGroupManager() {
        SearchFormGroupManager.add(DateSearchFormGroup);
        SearchFormGroupManager.add(TextSearchFormGroup);
        SearchFormGroupManager.add(NumberSearchFormGroup);
    }

    /**
     * Initialize cell formatter library
     * @private
     */
    _initCellFormatterManager() {
        CellFormatterManager.add(TextFormatter);
        CellFormatterManager.add(BooleanFormatter);
        CellFormatterManager.add(DateFormatter);
        CellFormatterManager.add(StatusFormatter);
        CellFormatterManager.add(SiteFormatter);
    }
}

// unique instance of Application
export default (new Application);
