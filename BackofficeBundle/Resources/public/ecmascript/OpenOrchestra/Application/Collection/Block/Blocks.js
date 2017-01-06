import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Block               from '../../Model/Block/Block'

/**
 * @class Blocks
 */
class Blocks extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Block;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_block_list_shared_table', urlParameter);
        }
    }
}

export default Blocks
