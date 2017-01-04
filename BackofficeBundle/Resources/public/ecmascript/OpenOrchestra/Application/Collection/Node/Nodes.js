import Node                from '../../Model/Node/Node'
import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'

/**
 * @class Nodes
 */
class Nodes extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Node;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'nodes': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_node_list', urlParameter);
            case "update":
                return Routing.generate('open_orchestra_api_node_update_children_order', urlParameter);
        }
    }
}

export default Nodes
