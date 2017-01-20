import OrchestraModel from '../OrchestraModel'
import Blocks         from '../../Collection/Block/Blocks'

/**
 * @class Area
 */
class Area extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('blocks')) {
            response.blocks = new Blocks(response.blocks);
        }

        return response;
    }
}

export default Area
