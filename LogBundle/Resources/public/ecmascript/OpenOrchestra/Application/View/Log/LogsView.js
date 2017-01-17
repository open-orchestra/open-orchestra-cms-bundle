import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import LogListView            from '../../View/Log/LogListView'

/**
 * @class LogsView
 */
class LogsView extends AbstractCollectionView
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
        let template = this._renderTemplate('Log/logsView');
        this.$el.html(template);
        this._listView = new LogListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.logs-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default LogsView;
