import AbstractFormView from 'OpenOrchestra/Service/Form/View/AbstractFormView'
import ApplicationError from 'OpenOrchestra/Service/Error/ApplicationError'
import FlashMessage     from 'OpenOrchestra/Service/FlashMessage/FlashMessage'
import FlashMessageBag  from 'OpenOrchestra/Service/FlashMessage/FlashMessageBag'

/**
 * @class NewNodeFormView
 */
class NewNodeFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  siteLanguages
     * @param {string} parentId
     * @param {string} language
     * @param {int}    order
     */
    initialize({form, siteLanguages, parentId, language, order}) {
        super.initialize({form : form});
        this._siteLanguages = siteLanguages;
        this._parentId = parentId;
        this._language = language;
        this._order = order;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Node/newNodeView',
            {
                language: this._language,
                parentId: this._parentId,
                order: this._order,
                siteLanguages: this._siteLanguages
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm(event) {
        return {
            '201': $.proxy(this._redirectNew, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }

    /**
     * Redirect after created new node
     *
     * @private
     */
    _redirectNew(data, textStatus, jqXHR) {
        let nodeId = jqXHR.getResponseHeader('nodeId');
        if (null === nodeId) {
            throw new ApplicationError('Invalid nodeId');
        }
        let url = Backbone.history.generateUrl('showNode', {nodeId: nodeId, language: this._language});
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
}

export default NewNodeFormView;
