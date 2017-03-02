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
        $('.datepicker', this.$el).datepicker({
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>'
        });

        return this;
    }
}

export default TrashItemsView;
