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
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
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
        let urlParameter = options.urlParameter || {};
        switch (context) {
            case "list_content_type_for_content":
                return Routing.generate('open_orchestra_api_content_type_list_for_content');
        }
    }
}

export default ContentTypes
