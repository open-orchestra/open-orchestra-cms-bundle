import DataTableCollection from 'OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import Group               from 'OpenOrchestra/Application/Model/Group/Group'

/**
 * @class UserGroups
 */
class UserGroups extends DataTableCollection
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
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_group_user_list');
        }
    }
}

export default UserGroups
