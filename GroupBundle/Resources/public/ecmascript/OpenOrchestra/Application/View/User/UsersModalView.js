import ModalView         from 'OpenOrchestra/Service/Modal/View/ModalView'
import UserListModalView from 'OpenOrchestra/Application/View/User/UserListModalView'

/**
 * @class UsersModalView
 */
class UsersModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        $.extend(this.events, {
            'click .select-user': '_selectUser',
            'click .search-engine button.submit, .search-engine button.reset': '_search'
        });
    }

    /**
     * Initialize
     * @param {membersView} membersView
     * @param {Object}      collection
     * @param {Object}      selected
     * @param {Object}      settings
     */
    initialize({membersView, collection, selected, settings}) {
        this._membersView = membersView;
        this._collection = collection;
        this._selected = selected;
        this._settings = settings;
    }

    /**
     * Render users view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_user_admin.user.title_list'),
                urlAdd: '#'+Backbone.history.generateUrl('newUser')
            });
            this.$el.append(template);
        } else {
            let template = this._renderTemplate('User/usersModalView');
            this.$el.append(template);
            this._listView = new UserListModalView({
                collection: this._collection,
                selected: this._selected,
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
        let filters = {};
        let formData = $('form.search-engine', this.$el).serializeArray();
        for (let data of formData) {
            filters[data.name] = data.value;
        }

        this._listView.filter(filters);

        return false;
    }

    /**
     * Select users
     */
    _selectUser() {
        let formUsers = _.pluck($('[name="user"]', this.$el).removeAttr('disabled').serializeArray(), 'value');
        let selectedUsers = [];
        for (let user of this._collection.models) {
            if (formUsers.indexOf(user.get('id')) > -1) {
                selectedUsers.push(user);
            }
        }
        this._membersView.trigger('user:select', selectedUsers);
        this.$el.modal('hide');
    }
}

export default UsersModalView;
