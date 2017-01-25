import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class KeywordFormView
 */
class KeywordFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} name

     */
    initialize({form, name}) {
        super.initialize({form : form});
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Keyword/keywordFormView', {
            name: this._name
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
        let name = jqXHR.getResponseHeader('name');
        if (null === keywordId || null === name) {
            throw new ApplicationError('Invalid keywordId or name');
        }
        let url = Backbone.history.generateUrl('editKeyword', {
            keywordId: keywordId,
            name: name
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

}

export default KeywordFormView;
