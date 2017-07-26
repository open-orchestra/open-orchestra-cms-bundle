import DataTableCollection from 'OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import TrashItem           from 'OpenOrchestra/Application/Model/TrashItem/TrashItem'

/**
 * @class TrashItems
 */
class TrashItems extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = TrashItem;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'trash_items': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_trashcan_list');
            case "delete":
                return Routing.generate('open_orchestra_api_trashcan_delete_multiple');
        }
    }
}

export default TrashItems
