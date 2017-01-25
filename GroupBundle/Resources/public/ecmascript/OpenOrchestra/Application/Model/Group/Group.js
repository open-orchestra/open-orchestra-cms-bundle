import OrchestraModel from '../OrchestraModel'
import Site           from '../Site/Site'

/**
 * @class Group
 */
class Group extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('site')) {
            response.site = new Site(response.site);
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "create":
                return Routing.generate('open_orchestra_api_group_duplicate');
            case "delete":
                urlParameter.groupId = this.get('id');
                return Routing.generate('open_orchestra_api_group_delete', urlParameter);
        }
    }
}

export default Group
