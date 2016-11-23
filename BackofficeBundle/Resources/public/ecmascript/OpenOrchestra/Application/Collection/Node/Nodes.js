import OrchestraCollection from '../OrchestraCollection'
import Node                from '../../Model/Node/Node'

/**
 * @class Nodes
 */
class Nodes extends OrchestraCollection
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
    _getSyncUrl(options) {
        let urlParameter = options.urlParameter || {};
        return {
            'update': Routing.generate('open_orchestra_api_node_update_children_order', urlParameter)
        }
    }
}

export default Nodes
