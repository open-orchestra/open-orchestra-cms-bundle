import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import UserFormView       from '../../View/User/UserFormView'
import FormBuilder        from '../../../Service/Form/Model/FormBuilder'

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

        return false;
    }

    /**
     * Edit User
     */
    editUser(userId) {
        let url = Routing.generate('open_orchestra_user_admin_user_form', {userId : userId});
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form : form, userId: userId});
            Application.getRegion('content').html(userFormView.render().$el);
        });

        return false;
    }
}

export default UserRouter;
