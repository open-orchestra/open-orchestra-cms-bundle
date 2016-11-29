import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Redirections     from '../../Collection/Redirection/Redirections'

/**
 * @class NodeFormView
 */
class NodeFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  siteLanguages
     * @param {string} siteId
     * @param {string} nodeId
     * @param {string} language
     */
    initialize({form, siteLanguages, siteId, nodeId, language}) {
        super.initialize({form : form});
        this._siteLanguages = siteLanguages;
        this._siteId = siteId;
        this._nodeId = nodeId;
        this._language = language;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Node/nodeEditView',
            {
                language: this._language,
                nodeId: this._nodeId,
                siteLanguages: this._siteLanguages
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();
        this._renderRedirections();

        return this;
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }

    _renderRedirections() {
        new Redirections().fetch({
            urlParameter: {
                locale: this._language,
                nodeId: this._nodeId,
                siteId: this._siteId
            },
            success: (redirections) => {
                if (redirections.length > 0) {
                    console.log(redirections);
                    let template = this._renderTemplate('Node/redirectionListView', { redirections: redirections.models });
                    $('.tab-seo', this.$el).append(template);
                }
            }
        });
    }
}

export default NodeFormView;
