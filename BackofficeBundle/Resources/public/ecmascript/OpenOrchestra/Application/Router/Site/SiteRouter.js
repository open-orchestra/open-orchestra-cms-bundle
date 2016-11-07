import OrchestraRouter from '../OrchestraRouter'
import app             from '../../Application'
import SiteListView    from '../../View/Site/SiteListView'
import Sites           from '../../Collection/Site/Sites'

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
            'site/list(/:page)': 'listSite'
        };
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listSite(page = 1) {
        this._diplayLoader(app.getRegion('content'));
        let collection = new Sites();
        let siteView = new SiteListView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = siteView.render().$el;
        app.getRegion('content').html(el);
    }
}

export default SiteRouter;
