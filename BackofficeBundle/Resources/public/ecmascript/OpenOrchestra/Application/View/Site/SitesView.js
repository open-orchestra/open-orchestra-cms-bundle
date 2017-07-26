import AbstractCollectionView from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import SiteListView           from 'OpenOrchestra/Application/View/Site/SiteListView'

/**
 * @class SitesView
 */
class SitesView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize({ removeMultiple: false });
    }

    /**
     * List view should be an instance of AbstractDataTableView
     *
     * @param {OrchestraCollection} collection
     * @param {Object}              settings
     * @param {boolean}             inPlatformContext
     */
    initialize({collection, settings, inPlatformContext}) {
        super.initialize({collection, settings});
        this._inPlatformContext = inPlatformContext;
    }

    /**
     * Render sites view
     */
    render() {
        let template = this._renderTemplate('Site/sitesView', {inPlatformContext: this._inPlatformContext});
        this.$el.html(template);
        this._listView = new SiteListView({
            collection: this._collection,
            settings: this._settings,
            inPlatformContext: this._inPlatformContext
        });
        $('.sites-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default SitesView;
