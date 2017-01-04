import OrchestraView from '../OrchestraView'
import SiteListView  from '../../View/Site/SiteListView'

/**
 * @class SitesView
 */
class SitesView extends OrchestraView
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
    initialize({collection, settings}) {
        this._collection = collection;
        this._settings = settings;
    }

    /**
     * Render sites view
     */
    render() {

        let template = this._renderTemplate('Site/sitesView');
        this.$el.html(template);
        this._listView = new SiteListView({
            collection: this._collection,
            settings: this._settings
        });
        $('.sites-list', this.$el).html(this._listView.render().$el);

        return this;
    }


    /**
     * Search node in list
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

export default SitesView;
