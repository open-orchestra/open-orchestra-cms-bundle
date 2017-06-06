import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import ApplicationError     from '../../../Service/Error/ApplicationError'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import FlashMessageBag      from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage         from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewContentFormView
 */
class NewContentFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} contentTypeId
     * @param {String} contentTypeName
     * @param {String} language
     * @param {Array}  siteLanguages
     */
    initialize({form, contentTypeId, contentTypeName, language, siteLanguages}) {
        super.initialize({form : form});
        this._contentTypeId = contentTypeId;
        this._contentTypeName = contentTypeName;
        this._language = language;
        this._siteLanguages = siteLanguages;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Content/newContentView', {
            contentTypeId  : this._contentTypeId,
            contentTypeName: this._contentTypeName,
            language       : this._language,
            siteLanguages  : this._siteLanguages,
            messages       : FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * Redirect to edit content view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let contentId = jqXHR.getResponseHeader('contentId');
        let version = jqXHR.getResponseHeader('version');
        if (null === contentId || null === version) {
            throw new ApplicationError('Invalid contentId or version');
        }
        let url = Backbone.history.generateUrl('editContent', {
            contentTypeId: this._contentTypeId,
            language: this._language,
            contentId: contentId,
            version: version
        });
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }

        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
}

export default NewContentFormView;
