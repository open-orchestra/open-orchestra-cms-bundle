import OrchestraModel from '../OrchestraModel'
import SiteAlias      from './SiteAlias'

/**
 * @class Site
 */
class Site extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('main_alias')) {
            response.main_alias = new SiteAlias(response.main_alias);
        }

        return response;
    }
}

export default Site
