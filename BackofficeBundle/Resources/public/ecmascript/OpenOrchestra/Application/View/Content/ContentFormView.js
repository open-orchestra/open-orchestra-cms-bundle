import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Content              from '../../Model/Content/Content'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class ContentFormView
 */
class ContentFormView extends mix(AbstractFormView).with(FormViewButtonsMixin) 
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     */
    initialize({form, name, contentTypeId, language, siteLanguageUrl, contentId = null}) {
        super.initialize({form : form});
        this._name = name;
        this._contentTypeId = contentTypeId;
        this._language = language;
        this._siteLanguageUrl = siteLanguageUrl;
        this._contentId = contentId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Content/contentEditView', {
            contentTypeId: this._contentTypeId,
            language: this._language,
            name: this._name,
            siteLanguageUrl: this._siteLanguageUrl,
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * Redirect to edit user view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let contentId = jqXHR.getResponseHeader('contentId');
        if (null === contentId) {
            throw new ApplicationError('Invalid contentId or name');
        }
        let url = Backbone.history.generateUrl('editContent', {
            contentTypeId: this._contentTypeId,
            language: this._language,
            contentId: contentId
        });
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        if (null === this._contentId) {
            throw new ApplicationError('Invalid contentId');
        }
        let content = new Content({'content_id': this._contentId});
        content.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listContent');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default ContentFormView;
