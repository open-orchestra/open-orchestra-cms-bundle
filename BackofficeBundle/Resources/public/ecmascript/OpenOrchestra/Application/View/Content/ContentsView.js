import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import SearchFormGroupManager from '../../../Service/Content/SearchFormGroupManager'
import Application            from '../../Application'
import ContentListView        from '../../View/Content/ContentListView'

/**
 * @class ContentsView
 */
class ContentsView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, urlParameter, contentType, statuses}) {
        this._collection = collection;
        this._settings = settings;
        this._urlParameter = urlParameter;
        this._contentType = contentType;
        this._statuses = statuses;
    }

    /**
     * Render contents view
     */
    render() {
        let statuses = this._statuses.toJSON();
        statuses = statuses.hasOwnProperty('statuses') ? statuses.statuses : [];
        let template = this._renderTemplate('Content/contentsView', {
            contentType: this._contentType.toJSON(),
            language: this._urlParameter.language,
            siteLanguages: Application.getContext().siteLanguages,
            statuses: statuses,
            SearchFormGroupManager: SearchFormGroupManager
        });
        this.$el.html(template);
        $.datepicker.setDefaults($.datepicker.regional[Application.getContext().language]);
        $('.datepicker', this.$el).datepicker({
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>'
        });
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
