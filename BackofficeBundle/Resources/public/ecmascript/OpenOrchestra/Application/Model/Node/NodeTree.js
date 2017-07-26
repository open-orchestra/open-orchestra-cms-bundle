import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'
import Node           from 'OpenOrchestra/Application/Model/Node/Node'

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
            response.node = new Node(response.node, {parse: true});
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
}

export default NodeTree
