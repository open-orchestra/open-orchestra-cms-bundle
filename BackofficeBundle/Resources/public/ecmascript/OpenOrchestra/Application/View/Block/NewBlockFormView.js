import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import ApplicationError from '../../../Service/Error/ApplicationError'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewBlockFormView
 */
class NewBlockFormView extends AbstractFormView
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
     * Initialize
     * @param {Form}   form
     * @param {string} language
     * @param {string} name
     */
    initialize({form, language, name}) {
        super.initialize({form : form});
        this._language = language;
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Block/newBlockView', {name: this._name});
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritdoc
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectEditBlock, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewBlock, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to edit block view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditBlock(data, textStatus, jqXHR) {
        let blockId = jqXHR.getResponseHeader('blockId');
        let blockLabel = jqXHR.getResponseHeader('blockLabel');
        if (null === blockId || null === blockLabel) {
            throw new ApplicationError('Invalid blockId or blockLabel');
        }
        let url = Backbone.history.generateUrl('editBlock', {blockId: blockId, blockLabel: blockLabel});
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Redirect to new workflow profile view
     *
     * @private
     */
    _redirectNewBlock(data) {
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.generateUrl('newBlockList', {language: this._language}));
    }
}

export default NewBlockFormView;
