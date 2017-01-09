import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import StatusListView         from '../../View/Status/StatusListView'
import Application            from '../../Application'

/**
 * @class StatusesView
 */
class StatusesView extends AbstractCollectionView
{
    /**
     * Render statuses view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_workflow_admin.status.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Status/statusesView',
            {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new StatusListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.statuses-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default StatusesView;
