import OrchestraCollection  from 'OpenOrchestra/Application/Collection/OrchestraCollection'
import BlockComponent       from 'OpenOrchestra/Application/Model/Block/BlockComponent'

/**
 * @class BlockComponents
 */
class BlockComponents extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = BlockComponent;
    }

    /**
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('collection_name')) {
            return response[response.collection_name];
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_block_list_block-component', urlParameter);
        }
    }
}

export default BlockComponents
