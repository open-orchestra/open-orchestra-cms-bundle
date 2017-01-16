import OrchestraModel from '../OrchestraModel'
import Fields         from './Fields'

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

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "create":
                return Routing.generate('open_orchestra_api_content_duplicate');
        }
    }
}

export default Content
