import OrchestraModel from '../OrchestraModel'
import Status         from '../Status/Status'

/**
 * @class WorkflowTransition
 */
class WorkflowTransition extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('status_from')) {
            response.status_from = new Status(response.status_from);
        }
        if (response.hasOwnProperty('status_to')) {
            response.status_to = new Status(response.status_to);
        }

        return response;
    }
}

export default WorkflowTransition
