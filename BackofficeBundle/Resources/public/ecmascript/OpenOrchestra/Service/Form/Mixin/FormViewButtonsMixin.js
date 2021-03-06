import FlashMessageBag  from 'OpenOrchestra/Service/FlashMessage/FlashMessageBag'
import FlashMessage     from 'OpenOrchestra/Service/FlashMessage/FlashMessage'
import ApplicationError from 'OpenOrchestra/Service/Error/ApplicationError'
import ConfirmModalView from 'OpenOrchestra/Service/ConfirmModal/View/ConfirmModalView'
import Application      from 'OpenOrchestra/Application/Application'

let FormViewButtonsMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize() {
        super.preinitialize();
        this.events = this.events || {};
        this.events['click button.submit-continue-form'] = '_submit';
        this.events['click button.delete-button:not(.disabled)'] = '_confirmDelete';
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
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }

    /**
     * Show modal confirm to delete models
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmDelete(event) {
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove.message'),
            yesCallback: this._deleteElement,
            context: this
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
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
