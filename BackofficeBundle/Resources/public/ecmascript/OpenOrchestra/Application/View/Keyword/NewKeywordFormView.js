import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import ApplicationError from '../../../Service/Error/ApplicationError'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewKeywordFormView
 */
class NewKeywordFormView extends AbstractFormView
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
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Keyword/keywordFormView',
            {
                title: Translator.trans('open_orchestra_backoffice.keyword.title_new')
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form', this.$el);
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
            '201': $.proxy(this._redirectEditKeyword, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewKeyword, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to edit keyword view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditKeyword(data, textStatus, jqXHR) {
        let keywordId = jqXHR.getResponseHeader('keywordId');
        if (null === keywordId) {
            throw new ApplicationError('Invalid keywordId');
        }
        let url = Backbone.history.generateUrl('editKeyword', {keywordId: keywordId});
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);

    }

    /**
     * Redirect to new keyword view
     *
     * @private
     */
    _redirectNewKeyword() {
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }
}

export default NewKeywordFormView;
