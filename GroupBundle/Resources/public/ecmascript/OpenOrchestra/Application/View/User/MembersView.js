import AbstractCollectionView from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import Application            from 'OpenOrchestra/Application/Application'
import MemberListView         from 'OpenOrchestra/Application/View/User/MemberListView'
import UsersModalView         from 'OpenOrchestra/Application/View/User/UsersModalView'
import Users                  from 'OpenOrchestra/Application/Collection/User/Users'

/**
 * @class MembersView
 */
class MembersView extends AbstractCollectionView
{
    /**
     * @param {Object} collection
     * @param {Array}  settings
     * @param {Array}  groupId
     */
    preinitialize({collection, settings, groupId}) {
        super.preinitialize({collection:collection, seottings:settings});
        this._groupId = groupId;
        this.events = {
            'click .btn-add' : '_showSelectUserModal'
        }
        this.on('user:select', this._addUsers);
    }

    /**
     * Render users view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_user_admin.user.title_list')
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('User/membersView');
            this.$el.html(template);
            this._listView = new MemberListView({
                collection: this._collection,
                groupId: this._groupId,
                settings: this._settings
            });
            $('.members-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }

    /**
     * show users modal
     *
     * @private
     */
    _showSelectUserModal() {
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        new Users().fetch({
            apiContext: 'user_list',
            data : {
                start: 0,
                length: pageLength
            },
            success: (users) => {
                let usersModalView = new UsersModalView({
                    membersView: this,
                    collection: users,
                    selected: this._collection,
                    settings: {
                        page: 0,
                        deferLoading: [users.recordsTotal, users.recordsFiltered],
                        data: users.models,
                        pageLength: pageLength
                    }
                });
                Application.getRegion('modal').html(usersModalView.render().$el);
                usersModalView.show();
            }
        });
    }

    /**
     * add users selected in modal
     */
    _addUsers(selectedUsers) {
        let users = new Users(selectedUsers);
        users.save({
            urlParameter: {
                groupId: this._groupId
            },
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default MembersView;
