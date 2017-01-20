import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import ApplicationError from '../../../Service/Error/ApplicationError'

let FormViewButtonsMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize() {
        super.preinitialize();
        this.events = this.events || {};
        this.events['click button.submit-continue-form'] = '_submit';
        this.events['click button.delete-button'] = '_deleteElement';
    }

    /**
     * @param {Object} event
     * 
     * @return {Object}
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectEditElement, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewElement, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to new workflow profile view
     *
     * @param {mixed}  data
     * @private
     */
    _redirectNewElement(data) {
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }

    /**
     * Redirect to new workflow profile view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        throw new ApplicationError('Must implement method _redirectEditElement');
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        throw new ApplicationError('Must implement method _deleteElement');
    }
};

export default FormViewButtonsMixin;
