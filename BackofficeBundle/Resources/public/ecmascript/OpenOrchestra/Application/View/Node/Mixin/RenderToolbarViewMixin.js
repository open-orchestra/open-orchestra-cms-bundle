import Statuses         from '../../../Collection/Status/Statuses'
import Nodes            from '../../../Collection/Node/Nodes'

import NodeToolbarView  from '../NodeToolbarView'

let RenderToolbarViewMixin = (superclass) => class extends superclass {

    /**
     * @param {Object} $selector
     * @param {string} routeName
     * @private
     */
    _renderNodeActionToolbar($selector, routeName) {
        this._displayLoader($selector);
        let statuses = new Statuses();
        let nodeVersions = new Nodes();
        $.when(
            statuses.fetch({
                apiContext: 'node',
                urlParameter: {
                    nodeId: this._node.get('node_id'),
                    siteId: this._node.get('site_id'),
                    language: this._node.get('language'),
                    version: this._node.get('version')
                }
            }),
            nodeVersions.fetch({
                apiContext: 'list-version',
                urlParameter: {
                    nodeId: this._node.get('node_id'),
                    language: this._node.get('language')
                }
            })
        ).done( () => {
            let nodeToolbarView = new NodeToolbarView(
                {
                    node: this._node,
                    statuses: statuses,
                    nodeVersions: nodeVersions,
                    routeName: routeName
                }
            );
            $selector.html(nodeToolbarView.render().$el);
        });
    }
};

export default RenderToolbarViewMixin;
