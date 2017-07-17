import OrchestraRouter from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application     from 'OpenOrchestra/Application/Application'
import FormBuilder     from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import Sites           from 'OpenOrchestra/Application/Collection/Site/Sites'
import SitesView       from 'OpenOrchestra/Application/View/Site/SitesView'
import SiteFormView    from 'OpenOrchestra/Application/View/Site/SiteFormView'

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
        }
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.site'),
                link: '#'+Backbone.history.generateUrl('listSite')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-site'
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
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        this._displayLoader(Application.getRegion('content'));
        let collection = new Sites();
        let sitesView = new SitesView({
            collection: collection,
            settings: {
                page: Number(page) - 1,
                pageLength: pageLength
            },
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
}

export default SiteRouter;
