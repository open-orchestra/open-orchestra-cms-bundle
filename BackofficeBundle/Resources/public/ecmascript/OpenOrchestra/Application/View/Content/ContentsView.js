import OrchestraView   from '../OrchestraView'
import Application     from '../../Application'
import ContentListView from '../../View/Content/ContentListView'

/**
 * @class ContentsView
 */
class ContentsView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, urlParameter, contentType}) {
        this._collection = collection;
        this._settings = settings;
        this._urlParameter = urlParameter;
        this._contentType = contentType;
    }

    /**
     * Render contents view
     */
    render() {
        let template = this._renderTemplate('Content/contentsView', {
            contentTypeName: this._urlParameter.contentTypeName,
            contentType: this._urlParameter.contentType,
            language: this._urlParameter.language,
            siteLanguages: Application.getContext().siteLanguages
        });
        this.$el.html(template);
        this._listView = new ContentListView({
            collection: this._collection,
            settings: this._settings,
            urlParameter: this._urlParameter,
            contentType: this._contentType
            
        });
        $('.contents-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default ContentsView;
