import NodeRouter              from './Router/Node/NodeRouter'
import KeywordRouter           from './Router/Keyword/KeywordRouter'
import DashboardRouter         from './Router/Dashboard/DashboardRouter'
import SiteRouter              from './Router/Site/SiteRouter'
import SitePlatformRouter      from './Router/Site/SitePlatformRouter'
import ContentTypeRouter       from './Router/ContentType/ContentTypeRouter'
import ContentRouter           from './Router/Content/ContentRouter'
import BlockRouter             from './Router/Block/BlockRouter'
import SharedBlockRouter       from './Router/Block/SharedBlockRouter'
import RedirectionRouter       from './Router/Redirection/RedirectionRouter'
import TrashItemRouter         from './Router/TrashItem/TrashItemRouter'

import HeaderView              from './View/Header/HeaderView'
import ErrorView               from './View/Error/ErrorView'
import NavigationManager       from '../Service/NavigationManager'
import MenuView                from './View/Menu/MenuView'
import BreadcrumbView          from './View/Breadcrumb/BreadcrumbView'
import NodeRestoreModalView    from './View/TrashItem/NodeRestoreModalView'
import ContentRestoreModalView from './View/TrashItem/ContentRestoreModalView'

import SitesAvailable          from './Collection/Site/SitesAvailable'

import FormBehaviorManager     from '../Service/Form/Behavior/Manager'
import PatchSubmit             from '../Service/Form/Behavior/PatchSubmit'
import PatchAndSendSubmit      from '../Service/Form/Behavior/PatchAndSendSubmit'
import ScrollTable             from '../Service/Form/Behavior/ScrollTable'
import Tooltip                 from '../Service/Form/Behavior/Tooltip'
import TagSelect2              from '../Service/Form/Behavior/TagSelect2'
import DatePicker              from '../Service/Form/Behavior/DatePicker'
import TreeChoice              from '../Service/Form/Behavior/TreeChoice'
import NodeTemplateSelection   from '../Service/Form/Behavior/NodeTemplateSelection'
import GenerateId              from '../Service/Form/Behavior/GenerateId'
import CollectionSortable      from '../Service/Form/Behavior/CollectionSortable'
import Accordion               from '../Service/Form/Behavior/Accordion'
import BlockVideoType          from '../Service/Form/Behavior/BlockVideoType'
import ColorPicker             from '../Service/Form/Behavior/ColorPicker'
import Tinymce                 from '../Service/Form/Behavior/Tinymce'
import TagCondition            from '../Service/Form/Behavior/TagCondition'
import ContentType             from '../Service/Form/Behavior/ContentType'
import InputFile               from '../Service/Form/Behavior/InputFile'

import SearchFormGroupManager  from '../Service/SearchFormGroup/Manager'
import DateSearchFormGroup     from '../Service/SearchFormGroup/DateForm'
import TextSearchFormGroup     from '../Service/SearchFormGroup/TextForm'
import NumberSearchFormGroup   from '../Service/SearchFormGroup/NumberForm'

import CellFormatterManager    from '../Service/DataFormatter/Manager'
import TextFormatter           from '../Service/DataFormatter/TextFormatter'
import BooleanFormatter        from '../Service/DataFormatter/BooleanFormatter'
import DateFormatter           from '../Service/DataFormatter/DateFormatter'
import StatusFormatter         from '../Service/DataFormatter/StatusFormatter'

import ApplicationError        from '../Service/Error/ApplicationError'
import TinymceManager          from '../Service/Tinymce/TinymceManager'

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
    }
}

// unique instance of Application
export default (new Application);
