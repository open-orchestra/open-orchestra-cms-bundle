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
                return this._getSyncReadUrl(options, urlParameter);
            case "update":
                return Routing.generate('open_orchestra_api_node_update_children_order', urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "list":
                return Routing.generate('open_orchestra_api_node_list', urlParameter);
            case "list-with-block-in-area":
                return Routing.generate('open_orchestra_api_node_list_with_block_in_area', urlParameter);
            case "usage-block":
                return Routing.generate('open_orchestra_api_node_list_usage_block', urlParameter);
        }
    }
}

export default Nodes
