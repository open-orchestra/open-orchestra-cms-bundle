import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import ApplicationError     from '../../../Service/Error/ApplicationError'
import Content              from '../../Model/Content/Content'
import Contents             from '../../Collection/Content/Contents'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ContentToolbarView   from './ContentToolbarView'
import FlashMessageBag      from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class NewContentFormView
 */
class NewContentFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} contentTypeId
     * @param {String} language
     * @param {Array}  siteLanguageUrl
     */
    initialize({form, contentTypeId, language, siteLanguageUrl}) {
        super.initialize({form : form});
        this._contentTypeId = contentTypeId;
        this._language = language;
        this._siteLanguageUrl = siteLanguageUrl;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Content/contentEditView', {
            contentTypeId  : this._contentTypeId,
            language       : this._language,
            siteLanguageUrl: this._siteLanguageUrl,
            messages       : FlashMessageBag.getMessages(),
            title          : Translator.trans('open_orchestra_backoffice.table.contents.new')
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
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
