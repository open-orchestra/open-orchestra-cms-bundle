import ModalView       from 'OpenOrchestra/Service/Modal/View/ModalView'
import Application     from 'OpenOrchestra/Application/Application'
import FormBuilder     from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import LoaderView      from 'OpenOrchestra/Application/View/Loader/LoaderView'
import NewUserFormView from 'OpenOrchestra/Application/View/User/NewUserFormView'

/**
 * @class NewUserModalView
 */
class NewUserModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click .create-user'] =  '_showCreateFormUser'
    }

    /**
     * Initialize
     *
     * @param {User} user
     */
    initialize({user}) {
        this._user = user;
    }

    /**
     * Render error
     */
    render() {
        let template = this._renderTemplate('User/newUserModalView', {
            user: this._user
        });
        this.$el.html(template);

        return this;
    }

    /**
     * Show form for create an user
     * @private
     */
    _showCreateFormUser() {
        let url = Routing.generate('open_orchestra_user_admin_new');
        Application.getRegion('content').html(new LoaderView().render().$el);
        FormBuilder.createFormFromUrl(url, (form) => {
            let userFormView = new NewUserFormView({form: form});
            Application.getRegion('content').html(userFormView.render().$el);
        }, this._user.toJSON());
        this.$el.modal('hide');
    }
}

export default NewUserModalView;
