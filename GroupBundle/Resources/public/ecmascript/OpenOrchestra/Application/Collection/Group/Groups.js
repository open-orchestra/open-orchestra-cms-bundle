import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Group               from '../../Model/Group/Group'

/**
 * @class Groups
 */
class Groups extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Group;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'groups': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_group_list');
            case "delete":
                return Routing.generate('open_orchestra_api_group_delete_multiple');
        }
    }
}

export default Groups
