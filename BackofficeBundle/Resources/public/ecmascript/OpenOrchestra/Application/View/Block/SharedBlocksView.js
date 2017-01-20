import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import SharedBlockListView    from './SharedBlockListView'

/**
 * @class SharedBlocksView
 */
class SharedBlocksView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize({ removeMultiple: false });
    }

    /**
     * @inheritdoc
     */
    initialize({collection, blockComponents, language, siteLanguages, settings}) {
        super.initialize({collection: collection, settings: settings});
        this._blockComponents = blockComponents;
        this._language = language;
        this._siteLanguages = siteLanguages;
    }

    /**
     * Render shared blocks view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_backoffice.shared_blocks.title_list'),
                urlAdd: '#'+Backbone.history.generateUrl('newBlockList', {language: this._language})
            });
            this.$el.html(template);
        } else {
            let categories = _.uniq(this._blockComponents.pluck('category'), false, (blockCategory) => {return blockCategory.get('key')});
            let template = this._renderTemplate('Block/sharedBlocksView', {
                language: this._language,
                siteLanguages: this._siteLanguages,
                categories: categories
            });
            this.$el.html(template);
            this._listView = new SharedBlockListView({
                collection: this._collection,
                language: this._language,
                settings: this._settings
            });
            $('.block-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default SharedBlocksView;
