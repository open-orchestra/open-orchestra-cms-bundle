import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'
import BlockCategory  from 'OpenOrchestra/Application/Model/Block/BlockCategory'

/**
 * @class Block
 */
class Block extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('category')) {
            response.category = new BlockCategory(response.category);
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "update":
                return Routing.generate('open_orchestra_api_block_share', urlParameter);
            case "delete":
                return this._getSyncDeleteUrl(options);
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncDeleteUrl(options) {
        let apiContext = options.apiContext || null;
        let urlParameter = options.urlParameter || {};
        urlParameter.blockId = this.get('id');
        switch (apiContext) {
            case "node":
                return Routing.generate('open_orchestra_api_node_delete_block', urlParameter);
            case "shared-block":
                return Routing.generate('open_orchestra_api_block_delete', urlParameter);
        }
    }
}

export default Block
