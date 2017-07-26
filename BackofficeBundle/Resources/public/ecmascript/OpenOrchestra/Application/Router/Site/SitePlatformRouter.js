import SiteRouter   from 'OpenOrchestra/Application/Router/Site/SiteRouter'
import Application  from 'OpenOrchestra/Application/Application'
import FormBuilder  from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import SiteFormView from 'OpenOrchestra/Application/View/Site/SiteFormView'

/**
 * @class SitePlatformRouter
 */
class SitePlatformRouter extends SiteRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'plateform/site/list(/:page)' : 'listPlatformSite',
            'plateform/site/edit/:siteId' : 'editPlatformSite',
            'plateform/site/new/'         : 'newPlatformSite'
        }
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.platform.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.site'),
                link: '#'+Backbone.history.generateUrl('listPlatformSite')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-platform-site'
        };
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listPlatformSite(page) {
        if (null === page) {
            page = 1
        }
        super.listSite(page, true);
    }

    /**
     * Edit site
     *
     * @param {string} siteId
     */
    editPlatformSite(siteId) {
        super.editSite(siteId, true);
    }

    /**
     * New site
     */
    newPlatformSite() {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_site_new');
        FormBuilder.createFormFromUrl(url, (form) => {
            let siteFormView = new SiteFormView({
                form: form,
                inPlatformContext : true
            });
            Application.getRegion('content').html(siteFormView.render().$el);
        });
    }
}

export default SitePlatformRouter;
