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
     * @param {Object} attrs
     *
     * @return string
     */
    modelId(attrs) {
        return attrs.id + _.uniqueId();
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options, urlParameter);
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
            case "list-table-shared-block":
                return Routing.generate('open_orchestra_api_block_list_shared_table', urlParameter);
            case "list-by-component-shared-block":
                return Routing.generate('open_orchestra_api_block_list_shared', urlParameter);
        }
    }
}

export default Blocks
