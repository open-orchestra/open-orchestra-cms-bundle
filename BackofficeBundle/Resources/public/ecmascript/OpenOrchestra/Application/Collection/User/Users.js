import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import User                from '../../Model/User/User'

/**
 * @class Users
 */
class Users extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = User;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'users': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_user_list');
            case "delete":
                return Routing.generate('open_orchestra_api_user_delete_multiple');
        }
    }
}

export default Users
