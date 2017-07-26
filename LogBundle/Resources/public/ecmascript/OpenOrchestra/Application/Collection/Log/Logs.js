import DataTableCollection from 'OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import Log                 from 'OpenOrchestra/Application/Model/Log/Log'

/**
 * @class Logs
 */
class Logs extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Log;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'logs': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_log_list');
        }
    }
}

export default Logs
