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
    preinitialize() {
        this.events = {
            'click .btn-delete': '_remove'
        }
    }

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
            contentTypeId: this._urlParameter.contentTypeId,
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
    /**
     * Remove
     *
     * @private
     */
    _remove() {
        let contents = this._collection.where({'delete': true});
        this._collection.destroyModels(contents, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default ContentsView;
