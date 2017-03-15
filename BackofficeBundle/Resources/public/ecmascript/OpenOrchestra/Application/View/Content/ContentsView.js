import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import SearchFormGroupManager from '../../../Service/SearchFormGroup/Manager'
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
        this._urlParameter = urlParameter;
        this._contentType = contentType;
        this._statuses = statuses;
        super.initialize({collection, settings});
    }

    /**
     * Render contents view
     */
    render() {
        $.datepicker.setDefaults($.datepicker.regional[Application.getContext().language]);
        let statuses = this._statuses.toJSON();
        statuses = statuses.hasOwnProperty('statuses') ? statuses.statuses : [];
        let template = this._renderTemplate('Content/contentsView', {
            contentType: this._contentType.toJSON(),
            language: this._urlParameter.language,
            siteLanguages: Application.getContext().siteLanguages,
            statuses: statuses,
            SearchFormGroupManager: SearchFormGroupManager,
            dateFormat: $.datepicker._defaults.dateFormat
        });
        this.$el.html(template);

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
}

export default ContentsView;
