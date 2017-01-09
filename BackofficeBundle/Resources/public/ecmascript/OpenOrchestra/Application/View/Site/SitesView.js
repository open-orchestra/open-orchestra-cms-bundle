import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import SiteListView           from '../../View/Site/SiteListView'

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
     * Render sites view
     */
    render() {
        let template = this._renderTemplate('Site/sitesView');
        this.$el.html(template);
        this._listView = new SiteListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.sites-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default SitesView;
