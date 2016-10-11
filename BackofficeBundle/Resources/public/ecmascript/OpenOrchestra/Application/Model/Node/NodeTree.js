import OrchestraModel from '../OrchestraModel'
import Node from './Node'
import App from '../../Application'

/**
 * @class NodeTree
 */
class NodeTree extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('node')) {
            response.node = new Node(response.node);
        }
        if (response.hasOwnProperty('children')) {
            let children = [];
            for (let nodeTree of response.children) {
                children.push(new NodeTree(this.parse(nodeTree)))
            }
            response.children = children;
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl() {
        let siteId = App.getConfiguration().getParameter('siteId');

        return {
            'read' : '-' + Routing.generate('open_orchestra_api_node_list_tree', {siteId : siteId})
        }
    }
}

export default NodeTree