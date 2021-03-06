import AbstractFormView     from 'OpenOrchestra/Service/Form/View/AbstractFormView'
import FormViewButtonsMixin from 'OpenOrchestra/Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class KeywordFormView
 */
class KeywordFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} keywordId
     */
    initialize({form, keywordId = null}) {
        super.initialize({form : form});
        this._keywordId = keywordId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = $('#oo_keyword_label', this._form.$form).val();
        if (null === this._keywordId) {
            title = Translator.trans('open_orchestra_backoffice.keyword.title_new');
        }
        let template = this._renderTemplate('Keyword/keywordFormView', {
            title: title
        });
        this.$el.html(template);
        this._$formRegion = $('.form', this.$el);
        super.render();

        return this;
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
        let keywordId = jqXHR.getResponseHeader('keywordId');
        let url = Backbone.history.generateUrl('editKeyword', {
            keywordId: keywordId
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

}

export default KeywordFormView;
