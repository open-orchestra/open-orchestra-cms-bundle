import OrchestraModel from '../OrchestraModel'
import Fields         from './Fields'
import Status         from '../Status/Status'

/**
 * @class Content
 */
class Content extends OrchestraModel
{

    /**
     * Pre initialize
     */
    preinitialize() {
        this.idAttribute = 'content_id';
    }

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
    _getSyncUrl(method) {
        switch (method) {
            case "create":
                return Routing.generate('open_orchestra_api_content_duplicate');
            case "delete":
                urlParameter.contentId = this.get('content_id');
                return Routing.generate('open_orchestra_api_content_delete', urlParameter);
        }
    }
}

export default Content
