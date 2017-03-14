import AbstractFormView       from '../../../Service/Form/View/AbstractFormView'
import Redirections           from '../../Collection/Redirection/Redirections'
import RenderToolbarViewMixin from './Mixin/RenderToolbarViewMixin'

/**
 * @class NodeFormView
 */
class NodeFormView extends mix(AbstractFormView).with(RenderToolbarViewMixin)
{
    /**
     * Initialize
     * @param {Node}   node
     * @param {Form}   form
     * @param {Array}  siteLanguages
     */
    initialize({node, siteLanguages, form}) {
        super.initialize({form : form});
        this._node = node;
        this._siteLanguages = siteLanguages;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Node/nodeEditView',
            {
                node : this._node,
                siteLanguages: this._siteLanguages
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();
        this._renderNodeActionToolbar($('.node-action-toolbar', this.$el), 'editNode');
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

    /**
     * @private
     */
    _renderRedirections() {
        new Redirections().fetch({
            urlParameter: {
                locale: this._node.get('language'),
                nodeId: this._node.get('node_id'),
                siteId: this._node.get('site_id')
            },
            success: (redirections) => {
                if (redirections.length > 0) {
                    let template = this._renderTemplate('Node/redirectionListView', { redirections: redirections.models });
                    $('.tab-seo', this.$el).append(template);
                }
            }
        });
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let node = new Node({id: this._nodeId});
        node.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('showNodes', {
                    language: this._language
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default NodeFormView;
