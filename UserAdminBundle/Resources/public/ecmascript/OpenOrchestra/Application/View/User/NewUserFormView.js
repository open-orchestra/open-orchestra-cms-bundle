import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import ApplicationError from '../../../Service/Error/ApplicationError'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewUserFormView
 */
class NewUserFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.submit-continue-form'] = '_submit';
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('User/newUserFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * @param  {Object} event
     *
     * @return {Object}
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectEditUser, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewUser, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to edit user view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditUser(data, textStatus, jqXHR) {
        let userId = jqXHR.getResponseHeader('userId');
        if (null === userId) {
            throw new ApplicationError('Invalid userId');
        }
        let url = Backbone.history.generateUrl('editUser', {userId: userId});
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);

    }

    /**
     * Redirect to new user view
     *
     * @param {mixed}  data
     * @private
     */
    _redirectNewUser(data) {
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }
}

export default NewUserFormView;
