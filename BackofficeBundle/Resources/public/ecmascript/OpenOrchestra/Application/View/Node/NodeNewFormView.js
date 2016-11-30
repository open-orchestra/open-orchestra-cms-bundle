import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class NodeNewFormView
 */
class NodeNewFormView extends AbstractFormView
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
        let template = this._renderTemplate('Node/nodeNewView',
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
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectNew, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }

    /**
     * Redirect after created new node
     *
     * @private
     */
    _redirectNew() {
        let url = Backbone.history.generateUrl('showNodes', {language: this._language});
        Backbone.history.navigate(url, true);
    }
}

export default NodeNewFormView;
