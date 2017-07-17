import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'
import BlockCategory  from 'OpenOrchestra/Application/Model/Block/BlockCategory'

/**
 * @class BlockComponent
 */
class BlockComponent extends OrchestraModel
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
}

export default BlockComponent
