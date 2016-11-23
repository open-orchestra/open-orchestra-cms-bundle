import OrchestraModel from '../OrchestraModel'
import Status         from '../Status/Status'

/**
 * @class Node
 */
class Node extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('status')) {
            response.status = new Status(response.status);
        }

        return response;
    }
}

export default Node
