import OrchestraRouter from '../OrchestraRouter'
import app             from '../../Application'
import UserFormView     from '../../View/User/UserFormView'
import FormBuilder      from '../../../Service/Form/Model/FormBuilder'

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
            'preference': '_showPreference'
        };
    }

    /**
     * Show Preference
     */
    _showPreference() {
        let url = Routing.generate('open_orchestra_user_admin_user_self_form', {userId : app.getContext().user.id});
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new UserFormView({form : form});
            app.getRegion('content').html(userFormView.render().$el);
        });

        return false;
    }
}

export default UserRouter;
