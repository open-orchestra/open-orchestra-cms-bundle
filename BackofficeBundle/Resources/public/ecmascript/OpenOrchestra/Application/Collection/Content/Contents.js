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
                return this._getSyncReadUrl(options, urlParameter);
            case "delete":
                return this._getSyncDeleteUrl(options, urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "list":
                return Routing.generate('open_orchestra_api_content_list', urlParameter);
            case "list-version":
                return Routing.generate('open_orchestra_api_content_list_version', urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncDeleteUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "delete-multiple-version":
                return Routing.generate('open_orchestra_api_content_delete_multiple_versions', urlParameter);
            case "delete-multiple":
                return Routing.generate('open_orchestra_api_content_delete_multiple');
        }
    }
}

export default Contents
