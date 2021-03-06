import AbstractCollectionView  from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import Application             from 'OpenOrchestra/Application/Application'
import KeywordListView         from 'OpenOrchestra/Application/View/Keyword/KeywordListView'

/**
 * @class KeywordsView
 */
class KeywordsView extends AbstractCollectionView
{
    /**
     * Render keywords view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_backoffice.keyword.title_list'),
                urlAdd: '#'+Backbone.history.generateUrl('newKeyword')
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Keyword/keywordsView',
                {
                    language: Application.getContext().get('language')
                });
            this.$el.html(template);
            this._listView = new KeywordListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.keyword-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default KeywordsView;
