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
}

export default Group
