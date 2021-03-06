import OrchestraCollection from 'OpenOrchestra/Application/Collection/OrchestraCollection'
import Site                from 'OpenOrchestra/Application/Model/Site/Site'

/**
 * @class SitesAvailable
 */
class SitesAvailable extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Site;
    }

    /**
     * @inheritdoc
     */
    parse(response) {
        if (response.hasOwnProperty('sites')) {
            return response.sites
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_available_site_list');
        }
    }
}

export default SitesAvailable
