import DataTableCollection from 'OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import Status              from 'OpenOrchestra/Application/Model/Status/Status'

/**
 * @class Statuses
 */
class Statuses extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Status;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'statuses': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options);
            case "delete":
                return Routing.generate('open_orchestra_api_status_delete_multiple');
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options) {
        let apiContext = options.apiContext || null;
        let urlParameter = options.urlParameter || {};
        switch (apiContext) {
            case "table":
                return Routing.generate('open_orchestra_api_status_list_table');
            case "nodes":
                return Routing.generate('open_orchestra_api_status_list');
            case "contents":
                return Routing.generate('open_orchestra_api_status_list');
            case "node":
                return Routing.generate('open_orchestra_api_node_list_status', urlParameter);
            case "content":
                return Routing.generate('open_orchestra_api_content_list_status', urlParameter);
        }
    }
}

export default Statuses
