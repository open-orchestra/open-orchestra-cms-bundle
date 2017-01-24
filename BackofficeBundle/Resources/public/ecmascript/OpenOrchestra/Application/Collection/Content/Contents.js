import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import Content             from '../../Model/Content/Content'

/**
 * @class Contents
 */
class Contents extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Content;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'contents': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_content_list', urlParameter);
            case "delete":
                return Routing.generate('open_orchestra_api_content_delete_multiple');
        }
    }
}

export default Contents
