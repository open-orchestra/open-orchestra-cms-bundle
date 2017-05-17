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
                    statuses: this._getAvailableStatuses(statuses),
                    nodeVersions: nodeVersions,
                    routeName: routeName
                }
            );
            $selector.html(nodeToolbarView.render().$el);
            this._renderMessageNodeActionToolbar($selector);
        });
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderMessageNodeActionToolbar($selector) {
        $('#node-message', $selector).html(this._renderTemplate('Node/nodeMessageToolbarView', {node: this._node}));
    }

    /**
     * @param  {Array} statuses
     * @return {Array}
     * @private
     */
    _getAvailableStatuses(statuses) {
        if (!this._node.get('rights').can_edit) {
            return new Statuses([]);
        }

        let result = [];
        for (let status of statuses.models) {
            if (this._node.get('status').get('id') != status.get('id') &&
                (this._node.get('rights').can_publish_node || !status.get('published_state'))) {
                result.push(status);
            }
        }

        return new Statuses(result);
    }
};

export default RenderToolbarViewMixin;
