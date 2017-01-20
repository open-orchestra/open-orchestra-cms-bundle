import OrchestraModel from '../OrchestraModel'
import BlockCategory  from './BlockCategory'

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
