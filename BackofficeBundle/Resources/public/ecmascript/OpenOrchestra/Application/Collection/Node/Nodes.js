import Node                from 'OpenOrchestra/Application/Model/Node/Node'
import DataTableCollection from 'OpenOrchestra/Service/DataTable/Collection/DataTableCollection'

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
            case "delete":
                return Routing.generate('open_orchestra_api_node_delete_multiple_versions', urlParameter);
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
            case "list-version":
                return Routing.generate('open_orchestra_api_node_list_version', urlParameter);
            case "list-with-block-in-area":
                return Routing.generate('open_orchestra_api_node_list_with_block_in_area', urlParameter);
            case "usage-block":
                return Routing.generate('open_orchestra_api_node_list_usage_block', urlParameter);
        }
    }
}

export default Nodes
