import OrchestraView from '../OrchestraView'
import GroupListView from '../../View/Group/GroupListView'
import Application   from '../../Application'

/**
 * @class GroupsView
 */
class GroupsView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit, click .search-engine button.reset': '_search',
            'click .btn-delete': '_remove'
        }
    }

    /**
     * @inheritdoc
     */
    initialize({collection, settings, sites}) {
        this._collection = collection;
        this._settings = settings;
        this._sites = sites;
    }

    /**
     * Render nodes view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_user_admin.group.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Group/groupsView',
                {
                    sites: this._sites.models,
                    language: Application.getContext().language
                }
            );
            this.$el.html(template);
            this._listView = new GroupListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.groups-list', this.$el).html(this._listView.render().$el);
        }

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
        let filters = {};
        if ($(event.target).hasClass('submit')) {
            $('button.reset', this.$el).removeClass('hidden');
        }
        if ($(event.target).hasClass('reset')) {
            $('button.reset', this.$el).addClass('hidden');
            $('form.search-engine', this.$el).trigger('reset');
        }

        let formData = $('form.search-engine', this.$el).serializeArray();
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
        let groups = this._collection.where({'delete': true});
        this._collection.destroyModels(groups, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default GroupsView;
