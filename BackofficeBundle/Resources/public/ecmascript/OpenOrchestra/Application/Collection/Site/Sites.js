import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Site                from '../../Model/Site/Site'

/**
 * @class Sites
 */
class Sites extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Site;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_site_list');
        }
    }
}

export default Sites
