import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import ContentType         from '../../Model/ContentType/ContentType'

/**
 * @class ContentTypes
 */
class ContentTypes extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = ContentType;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'content_types': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options);
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options) {
        let context = options.context || null;
        switch (context) {
            case "list_content_type_for_content":
                return Routing.generate('open_orchestra_api_content_type_list_for_content');
            case "list":
                return Routing.generate('open_orchestra_api_content_type_list');
            case "delete":
                return Routing.generate('open_orchestra_api_content_type_delete_multiple');
        }
    }
}

export default ContentTypes
