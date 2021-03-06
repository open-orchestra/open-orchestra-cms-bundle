import AbstractFormView          from 'OpenOrchestra/Service/Form/View/AbstractFormView'
import Redirections              from 'OpenOrchestra/Application/Collection/Redirection/Redirections'
import RenderToolbarViewMixin    from 'OpenOrchestra/Application/View/Node/Mixin/RenderToolbarViewMixin'
import TrashFormViewButtonsMixin from 'OpenOrchestra/Service/Form/Mixin/TrashFormViewButtonsMixin'

/**
 * @class NodeFormView
 */
class NodeFormView extends mix(AbstractFormView).with(RenderToolbarViewMixin, TrashFormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Node}    node
     * @param {Array}   siteLanguages
     * @param {Form}    form
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
     * Refresh render
     */
    refreshRender() {
        Backbone.Events.trigger('form:deactivate', this);
        this._renderForm();
        this._node.fetch({
            urlParameter: {
                'language': this._node.get('language'),
                'nodeId': this._node.get('node_id'),
                'siteId': this._node.get('site_id'),
                'version': this._node.get('version')
            },
            success: () => {
                this._renderMessageNodeActionToolbar($('.node-action-toolbar', this.$el));
            }
        });
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
        this._node.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('showNodes', {
                    language: this._node.get('language')
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default NodeFormView;
