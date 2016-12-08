import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Sites           from '../../Collection/Site/Sites'
import SiteListView    from '../../View/Site/SiteListView'
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
        };
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listSite(page = 1) {
        this._diplayLoader(Application.getRegion('content'));
        let collection = new Sites();
        let siteView = new SiteListView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = siteView.render().$el;
        Application.getRegion('content').html(el);
    }

    /**
     * Edit site
     *
     * @param {string} siteId
     */
    editSite(siteId) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_site_form', {
            siteId : siteId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let siteFormView = new SiteFormView({
                form : form
            });
            Application.getRegion('content').html(siteFormView.render().$el);
        });
    }
}

export default SiteRouter;
