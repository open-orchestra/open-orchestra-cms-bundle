import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import UserFormView    from '../../View/User/UserFormView'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'

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
            'user/preference': 'showUserPreference'
        };
    }

    /**
     * Show User Preference
     */
    showUserPreference() {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_user_admin_user_self_form', {userId : Application.getContext().user.id});
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form : form});
            Application.getRegion('content').html(userFormView.render().$el);
        });

        return false;
    }
}

export default UserRouter;
