import OrchestraModel from '../OrchestraModel'
import SiteAlias      from './SiteAlias'

/**
 * @class Site
 */
class Site extends OrchestraModel
{

    /**
     * Pre initialize
     */
    preinitialize() {
        this.idAttribute = 'site_id';
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.siteId = this.get('site_id');
        switch (method) {
            case "delete":
                return Routing.generate('open_orchestra_api_site_delete', urlParameter);
        }
    }

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
