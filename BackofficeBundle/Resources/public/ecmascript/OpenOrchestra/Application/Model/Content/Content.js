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
            case "create":
                return Routing.generate('open_orchestra_api_content_duplicate');
            case "delete":
                urlParameter.contentId = this.get('id');
                return Routing.generate('open_orchestra_api_content_delete', urlParameter);
        }
    }
}

export default Content
