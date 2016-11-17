import OrchestraCollection from '../OrchestraCollection'
import Status             from '../../Model/Status/Status'

/**
 * @class Statuses
 */
class Statuses extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Status;
    }

    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('statuses')) {
            return response.statuses
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl() {
        return {
            'read': Routing.generate('open_orchestra_api_status_list')
        }
    }
}

export default Statuses
