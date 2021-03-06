import OrchestraModel from 'OpenOrchestra/Application/Model/OrchestraModel'
import SiteAlias      from 'OpenOrchestra/Application/Model/Site/SiteAlias'

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
        switch (method) {
            case "delete":
                urlParameter.siteId = this.get('site_id');
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
