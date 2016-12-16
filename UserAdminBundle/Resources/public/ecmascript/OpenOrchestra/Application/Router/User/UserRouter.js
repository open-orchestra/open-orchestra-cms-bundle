import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import UserFormView    from '../../View/User/UserFormView'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Users           from '../../Collection/User/Users'
import UsersView       from '../../View/User/UsersView'
import NewUserView     from '../../View/User/NewUserView'
import NewUserFormView from '../../View/User/NewUserFormView'

/**
 * @class UserRouter
 */
class UserRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'user/selfedit': 'editSelfUser',
            'user/edit/:userId': 'editUser',
            'user/list(/:page)': 'listUser',
            'user/new/': 'newUser'
        };
    }

    /**
     * Edit user preference
     */
    editSelfUser() {
        let url = Routing.generate('open_orchestra_user_admin_user_self_form');
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form : form});
            Application.getRegion('content').html(userFormView.render().$el);
        });
    }

    /**
     * Edit User
     *
     * @param  {String} userId
     */
    editUser(userId) {
        let url = Routing.generate('open_orchestra_user_admin_user_form', {userId: userId});
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form: form});
            Application.getRegion('content').html(userFormView.render().$el);
        });
    }

    /**
     *  List User
     *
     * @param {String} page
     */
    listUser(page) {
        if (null === page) {
            page = 1
        }
        this._diplayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new Users().fetch({
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (users) => {
                let usersView = new UsersView({
                    collection: users,
                    settings: {
                        page: page,
                        deferLoading: [users.recordsTotal, users.recordsFiltered],
                        data: users.models,
                        pageLength: pageLength
                    }
                });
                let el = usersView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }

    /**
     * New user
     */
    newUser() {
        this._diplayLoader(Application.getRegion('content'));
        let newUserView = new NewUserView();
        Application.getRegion('content').html(newUserView.render().$el);
    }
}

export default UserRouter;
