import OrchestraModel from '../OrchestraModel'
import Fields         from './Fields'
import Status         from '../Status/Status'

/**
 * @class Content
 */
class Content extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('attributes')) {
            response.fields = new Fields(response.attributes);
        }
        if (response.hasOwnProperty('status')) {
            response.status = new Status(response.status);
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                urlParameter.contentId = this.get('id');
                return Routing.generate('open_orchestra_api_content_show', urlParameter);
            case "create":
                return this._getSyncCreateUrl(options, urlParameter);
            case "update":
                return this._getSyncUpdateUrl(options);
            case "delete":
                urlParameter.contentId = this.get('id');
                return Routing.generate('open_orchestra_api_content_delete', urlParameter);
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncUpdateUrl(options) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "update_status_with_save_published":
                return Routing.generate('open_orchestra_api_content_update_status_with_save_published');
            case "update_status":
                return Routing.generate('open_orchestra_api_content_update_status');
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncCreateUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "new-version":
                return Routing.generate('open_orchestra_api_content_new_version', urlParameter);
            case "new-language":
                return Routing.generate('open_orchestra_api_content_new_language', urlParameter);
            default:
                return Routing.generate('open_orchestra_api_content_duplicate', urlParameter);
        }
    }
}

export default Content
