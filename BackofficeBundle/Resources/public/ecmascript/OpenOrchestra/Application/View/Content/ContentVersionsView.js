import AbstractCollectionView  from '../../../Service/DataTable/View/AbstractCollectionView'
import ContentVersionsListView from './ContentVersionsListView'

/**
 * @class ContentVersionsView
 */
class ContentVersionsView extends AbstractCollectionView
{
    /**
     * @param {OrchestraCollection} collection
     * @param {string}              contentId
     * @param {string}              language
     */
    initialize({collection, contentId, language}) {
        super.initialize({collection: collection});
        this._contentId = contentId;
        this._language = language
    }

    /**
     * @inheritDoc
     */
    _toggleButtonDelete() {
        super._toggleButtonDelete();
        let models = this._collection.where({'delete': true});
        if (models.length >= this._collection.length) {
            $('.btn-delete', this.$el).addClass('disabled');
        }
    }

    /**
     * Render content versions
     */
    render() {
        let template = this._renderTemplate('Content/contentVersionsView', {
            collection: this._collection
        });
        this.$el.html(template);

        this._listView = new ContentVersionsListView({
            collection: this._collection,
            settings: {
                serverSide: false,
                processing: false,
                data: this._collection.models
            }
        });
        $('.content-versions-list', this.$el).html(this._listView.render().$el);

        return this;
    }

    /**
     * Remove
     *
     * @private
     */
    _remove() {
        if (null === this._listView) {
            throw TypeError("Parameter listView should be an instance of AbstractDataTableView");
        }
        let models = this._collection.where({'delete': true});
        this._collection.destroyModels(models, {
            apiContext: 'delete-multiple-version',
            urlParameter: {
                contentId: this._contentId,
                language: this._language
            },
            success: () => {
                this._listView.api.rows().clear();
                this._listView.api.rows.add(this._collection.models).draw();
                this._toggleButtonDelete();
            }
        });
    }
}

export default ContentVersionsView;
