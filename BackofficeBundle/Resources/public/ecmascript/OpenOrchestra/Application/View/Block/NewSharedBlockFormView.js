import AbstractNewBlockFormView from './AbstractNewBlockFormView'
import ApplicationError         from '../../../Service/Error/ApplicationError'
import FlashMessageBag          from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage             from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewSharedBlockFormView
 */
class NewSharedBlockFormView extends AbstractNewBlockFormView
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {string} name
     * @param {string} language
     */
    initialize({form, name, language}) {
        super.initialize({form, name});
        this._language = language;
    }

    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBackList() {
        return Translator.trans('open_orchestra_backoffice.back_to_list');
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        return Backbone.history.generateUrl('newSharedBlockListComponent',{
            language: this._language
        });
    }

    /**
     * @inheritdoc
     */
    _getUrlButtonBackList() {
        return Backbone.history.generateUrl('listSharedBlock',{
            language: this._language
        });
    }

    /**
     * @inheritdoc
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectEditSharedBlock, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewSharedBlock, this);
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
    _redirectEditSharedBlock(data, textStatus, jqXHR) {
        let blockId = jqXHR.getResponseHeader('blockId');
        let blockLabel = jqXHR.getResponseHeader('blockLabel');
        if (null === blockId || null === blockLabel) {
            throw new ApplicationError('Invalid blockId or blockLabel');
        }
        let url = Backbone.history.generateUrl('editSharedBlock', {
            blockId: blockId,
            blockLabel: blockLabel,
            language: this._language
        });
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Redirect to new shared block view
     *
     * @private
     */
    _redirectNewSharedBlock(data) {
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(Backbone.history.generateUrl('newSharedBlockListComponent', {language: this._language}), true);
    }
}

export default NewSharedBlockFormView;
