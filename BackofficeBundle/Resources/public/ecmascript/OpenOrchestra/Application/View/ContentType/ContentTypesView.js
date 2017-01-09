import OrchestraView         from '../OrchestraView'
import ContentTypesListView  from './ContentTypesListView'
import Application           from '../../Application'

/**
 * @class ContentTypesView
 */
class ContentTypesView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit': '_search',
            'click .btn-delete': '_remove'
        }
    }

    /**
     * @inheritdoc
     */
    initialize({collection, settings}) {
        this._collection = collection;
        this._settings = settings;
    }

    /**
     * Render content types view
     */
    render() {
        let template = this._renderTemplate('ContentType/contentTypesView', {
            language: Application.getContext().language
        });
        this.$el.html(template);
        this._listView = new ContentTypesListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.content-types-list', this.$el).html(this._listView.render().$el);

        return this;
    }


    /**
     * Search content types in list
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

    /**
     * Remove
     *
     * @private
     */
    _remove() {
        let contentTypes = this._collection.where({'delete': true});
        this._collection.destroyModels(contentTypes, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default ContentTypesView;
