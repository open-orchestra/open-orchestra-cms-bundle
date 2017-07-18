import OrchestraView         from 'OpenOrchestra/Application/View/OrchestraView'
import Application           from 'OpenOrchestra/Application/Application'
import User                  from 'OpenOrchestra/Application/Model/User/User'
import NewUserModalView      from 'OpenOrchestra/Application/View/User/NewUserModalView'
import ExistingUserModalView from 'OpenOrchestra/Application/View/User/ExistingUserModalView'
import FlashMessageBag       from 'OpenOrchestra/Service/FlashMessage/FlashMessageBag'


/**
 * @class NewUserView
 */
class NewUserView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .submit-form': '_checkCorrespondence'
        }
    }

    /**
     * Render view
     */
    render() {
        let template = this._renderTemplate('User/newUserView', {messages: FlashMessageBag.getMessages()});
        this.$el.html(template);

        return this;
    }

    /**
     * Check correspondence
     *
     * @returns {boolean}
     * @private
     */
    _checkCorrespondence(event) {
        let $form = $('form', this.$el);
        if ($form.get(0).checkValidity()) {
            event.preventDefault();
            let data = $('form', this.$el).serializeArray().reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
            let user = new User(data);
            user.fetch({
                success: (user) => {
                    let modal = new NewUserModalView({user: user});
                    if (typeof user.get('id') !== 'undefined') {
                       modal = new ExistingUserModalView({user: user});
                    }

                    Application.getRegion('modal').html(modal.render().$el);
                    modal.show();
                }
            });
        }
    }
}

export default NewUserView;
