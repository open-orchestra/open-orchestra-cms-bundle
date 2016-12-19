import OrchestraView  from '../OrchestraView'
import StatusListView from '../../View/Status/StatusListView'
import Application    from '../../Application'

/**
 * @class StatusesView
 */
class StatusesView extends OrchestraView
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
     * Render nodes view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_workflow_admin.status.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Status/statusesView',
            {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new StatusListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.statuses-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }


    /**
     * Search status in list
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
        let statuses = this._collection.where({'delete': true});
        this._collection.destroyModels(statuses, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default StatusesView;
