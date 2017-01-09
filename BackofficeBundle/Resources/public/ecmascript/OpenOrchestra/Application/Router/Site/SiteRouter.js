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
            'site/edit/:siteId/:name': 'editSite'
        };
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
     * List Site
     *
     * @param {int} page
     */
    listSite(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let collection = new Sites();
        let sitesView = new SitesView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = sitesView.render().$el;
        Application.getRegion('content').html(el);
    }

    /**
     * Edit site
     *
     * @param {string} siteId
     */
    editSite(siteId, name) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_site_form', {
            siteId : siteId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let siteFormView = new SiteFormView({
                form: form,
                name: name,
                siteId: siteId
            });
            Application.getRegion('content').html(siteFormView.render().$el);
        });
    }
}

export default SiteRouter;
