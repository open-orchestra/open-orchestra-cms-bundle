import OrchestraView   from '../OrchestraView'
import ContentListView from '../../View/Content/ContentListView'

/**
 * @class ContentsView
 */
class ContentsView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings}) {
        this._collection = collection;
        this._settings = settings;
    }

    /**
     * Render contents view
     */
    render() {
        let template = this._renderTemplate('Content/contentsView', {
            contentTypeName: this._settings.contentTypeName
        });
        this.$el.html(template);
        this._listView = new ContentListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.contents-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default ContentsView;
