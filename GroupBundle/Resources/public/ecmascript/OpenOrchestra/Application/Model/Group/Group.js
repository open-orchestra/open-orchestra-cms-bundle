import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'
import Site           from 'OpenOrchestra/Application/Model/Site/Site'

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
    _getSyncUrl(method, options) {
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
