import OrchestraModel from '../OrchestraModel'

/**
 * @class Area
 */
class Block extends OrchestraModel
{

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "delete":
                urlParameter.blockId = this.get('id');
                return Routing.generate('open_orchestra_api_node_delete_block', urlParameter);
        }
    }
}

export default Block
