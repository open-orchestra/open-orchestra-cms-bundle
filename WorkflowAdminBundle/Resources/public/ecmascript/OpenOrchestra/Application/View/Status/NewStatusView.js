import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Status           from '../../Model/Status/Status'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewStatusView
 */
class NewStatusView extends AbstractFormView
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
     * Render view
     */
    render() {
        let template = this._renderTemplate('Status/newStatusView', {messages: FlashMessageBag.getMessages()});
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
            '201': $.proxy(this._redirectEditStatus, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewStatus, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to edit status view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditStatus(data, textStatus, jqXHR) {
        let statusId = jqXHR.getResponseHeader('statusId');
        if (null === statusId) {
            throw new ApplicationError('Invalid statusId');
        }
        let url = Backbone.history.generateUrl('editStatus', {statusId: statusId});
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Redirect to new status view
     *
     * @param {mixed}  data
     * @private
     */
    _redirectNewStatus(data) {
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }
}

export default NewStatusView;
