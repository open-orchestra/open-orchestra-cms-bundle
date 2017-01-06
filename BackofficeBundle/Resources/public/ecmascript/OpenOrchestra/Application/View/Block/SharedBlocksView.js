import OrchestraView       from '../OrchestraView'
import SharedBlockListView from './SharedBlockListView'

/**
 * @class SharedBlocksView
 */
class SharedBlocksView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit': '_search'
        }
    }

    /**
     * @inheritdoc
     */
    initialize({collection, language, siteLanguages, settings}) {
        this._collection = collection;
        this._language = language;
        this._siteLanguages = siteLanguages;
        this._settings = settings;
    }

    /**
     * Render sites view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_backoffice.shared_blocks.title_list'),
                urlAdd: '#'+Backbone.history.generateUrl('newBlockList', {language: this._language})
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Block/sharedBlocksView', {
                language: this._language,
                siteLanguages: this._siteLanguages
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

    /**
     * Search shared block in list
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();

        let formData = $('form.search-engine', this.$el).serializeArray();
        let filters = {};
        for (let data of formData) {
            filters[data.name] = data.value;
        }
        this._listView.filter(filters);

        return false;
    }
}

export default SharedBlocksView;
