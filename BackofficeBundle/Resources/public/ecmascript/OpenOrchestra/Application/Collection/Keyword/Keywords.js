import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Keyword             from '../../Model/Keyword/Keyword'

/**
 * @class Keywords
 */
class Keywords extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Keyword;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'keywords': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_keyword_list');
            case "delete":
                return Routing.generate('open_orchestra_api_keyword_delete_multiple');
        }
    }
}

export default Keywords
