import AbstractCollectionView  from '../../../Service/DataTable/View/AbstractCollectionView'
import ContentVersionsListView from './ContentVersionsListView'
import ApplicationError        from '../../../Service/Error/ApplicationError'

/**
 * @class ContentVersionsView
 */
class ContentVersionsView extends AbstractCollectionView
{
    /**
     * @param {OrchestraCollection} collection
     * @param {Object}              settings
     * @param {string}              contentId
     * @param {string}              language
     * @param {string}              contentTypeId
     * @param {Object}              siteLanguages
     */
    initialize({collection, settings, contentId, language, contentTypeId, siteLanguages}) {
        super.initialize({collection: collection, settings: settings});
        this._contentId = contentId;
        this._contentTypeId = contentTypeId;
        this._language = language;
        this._siteLanguages = siteLanguages;
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
        let content = this._collection.first();
        if (typeof content === 'undefined') {
            throw new ApplicationError('A content should have at least one version')
        }
        let title = content.get('name');
        let template = this._renderTemplate('Content/contentVersionsView', {
            collection: this._collection,
            contentId: this._contentId,
            contentTypeId: this._contentTypeId,
            language: this._language,
            siteLanguages: this._siteLanguages,
            title: title
        });
        this.$el.html(template);

        let settings = $.extend(true, this._settings, {
                serverSide: false,
                processing: false,
                data: this._collection.models
            }
        );

        this._listView = new ContentVersionsListView({
            collection: this._collection,
            settings: settings,
            contentId: this._contentId,
            contentTypeId: this._contentTypeId,
            language: this._language
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
            success: () => {
                this._listView.api.clear();
                this._listView.api.rows.add(this._collection.models);
                this._listView.api.draw();
                this._toggleButtonDelete();
            }
        });
    }
}

export default ContentVersionsView;
