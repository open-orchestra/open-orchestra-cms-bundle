import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import ContentTypesListView   from './ContentTypesListView'
import Application            from '../../Application'

/**
 * @class ContentTypesView
 */
class ContentTypesView extends AbstractCollectionView
{
    /**
     * Render content types view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_backoffice.content_type.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('ContentType/contentTypesView', {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new ContentTypesListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.content-types-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default ContentTypesView;
