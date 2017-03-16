import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Sites           from '../../Collection/Site/Sites'
import SitesView       from '../../View/Site/SitesView'
import SiteFormView    from '../../View/Site/SiteFormView'

/**
 * @class SiteRouter
 */
class SiteRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'site/list(/:page)': 'listSite',
            'site/edit/:siteId': 'editSite',
            'site/new/'        : 'newSite'
        }
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.site'),
                link: '#'+Backbone.history.generateUrl('listSite')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getNavigationHighlight() {
        return {
            listSite : 'course-site',
            editSite : 'course-site',
            newSite  : 'course-site'
        };
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listSite(page, inPlatformContext) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let collection = new Sites();
        let sitesView = new SitesView({
            collection: collection,
            settings: {page: Number(page) - 1},
            inPlatformContext : inPlatformContext
        });
        let el = sitesView.render().$el;
        Application.getRegion('content').html(el);
    }

    /**
     * Edit site
     *
     * @param {string} siteId
     */
    editSite(siteId, inPlatformContext) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_site_form', {
            siteId : siteId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let siteFormView = new SiteFormView({
                form: form,
                siteId: siteId,
                inPlatformContext : inPlatformContext
            });
            Application.getRegion('content').html(siteFormView.render().$el);
        });
    }

    /**
     * New site
     */
    newSite() {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_site_new');
        FormBuilder.createFormFromUrl(url, (form) => {
            let siteFormView = new SiteFormView({
                form: form,
                inPlatformContext : false
            });
            Application.getRegion('content').html(siteFormView.render().$el);
        });
    }
}

export default SiteRouter;
