import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Status              from '../../Model/Status/Status'

/**
 * @class Statuses
 */
class Statuses extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Status;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'statuses': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_status_list_table');
            case "delete":
                return Routing.generate('open_orchestra_api_status_delete_multiple');
        }
    }
}

export default Statuses
