import AbstractCollectionView  from '../../../Service/DataTable/View/AbstractCollectionView'
import Application             from '../../Application'
import TrashItemListView       from './TrashItemListView'

/**
 * @class TrashItemsView
 */
class TrashItemsView extends AbstractCollectionView
{
    /**
     * Render trash items view
     */
    render() {

        let template = this._renderTemplate('TrashItem/trashItemsView');
        this.$el.html(template);
        this._listView = new TrashItemListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.trash-item-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default TrashItemsView;
