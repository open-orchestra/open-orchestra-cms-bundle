import AbstractDataFormatter from 'OpenOrchestra/Service/DataFormatter/AbstractDataFormatter'
import SitesAvailable        from 'OpenOrchestra/Application/Collection/Site/SitesAvailable'

/**
 * @class SiteFormatter
 */
class SiteFormatter extends AbstractDataFormatter
{
    /**
     * Initialize
     */
    initialize() {
        new SitesAvailable().fetch({
            success: (sites) => {
                this.sites = sites;
            }
        });
    }

    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'site';
    }

    /**
     * format the value
     *
     * @param  {string} value
     * @return string
     */
    format(value) {
        let site = this.sites.findWhere({site_id: value});
        if (typeof site != 'undefined') {
            return site.get('name');
        }

        return '';
    }
}

export default (new SiteFormatter);
