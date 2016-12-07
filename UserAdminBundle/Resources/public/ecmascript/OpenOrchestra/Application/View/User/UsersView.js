import OrchestraView from '../OrchestraView'
import UserListView  from '../../View/User/UserListView'

/**
 * @class UsersView
 */
class UsersView extends OrchestraView
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
                title: Translator.trans('open_orchestra_user_admin.user.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('User/usersView');
            this.$el.html(template);
            this._listView = new UserListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.users-list', this.$el).html(this._listView.render().$el);
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
        let users = this._collection.where({'delete': true});
        this._collection.destroyModels(users);
    }
}

export default UsersView;
