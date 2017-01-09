import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import UserListView           from '../../View/User/UserListView'

/**
 * @class UsersView
 */
class UsersView extends AbstractCollectionView
{
    /**
     * Render users view
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
}

export default UsersView;
