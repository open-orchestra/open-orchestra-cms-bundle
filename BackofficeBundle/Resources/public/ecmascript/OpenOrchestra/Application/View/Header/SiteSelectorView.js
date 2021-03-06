import OrchestraView    from 'OpenOrchestra/Application/View/OrchestraView'
import Application      from 'OpenOrchestra/Application/Application'

/**
 * @class SiteSelectorView
 */
class SiteSelectorView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'site-selector';
    }

    /**
     * @param {SitesAvailable} sites
     */
    initialize({sites}) {
        this.sites = sites;
    }

    /**
     * Render Site selector
     */
    render() {
        let currentSite = this._getCurrentSite();
        if (0 === this.sites.length || null == currentSite) {
            return this;
        }
        let linkMainAlias = this._getLinkSiteAlias(currentSite.get('main_alias'));
        let template = this._renderTemplate(
            'Header/siteSelectorView',
            {
                currentSite: currentSite,
                sites: _.without(this.sites.models, currentSite),
                currentLocale: Application.getContext().get('language'),
                linkMainAlias: linkMainAlias
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * @param {SiteAlias} siteAlias
     *
     * @return {String}
     * @private
     */
    _getLinkSiteAlias(siteAlias) {
        let link = siteAlias.get('scheme') + '://' + siteAlias.get('domain');
        if (siteAlias.has('prefix')) {
            link = link + '/' + siteAlias.get('prefix');
        }

        return link;
    }

    /**
     * @return Site|null
     */
    _getCurrentSite() {
        let currentSiteId = Application.getContext().get('siteId');
        for (let site of this.sites.models) {
            if (currentSiteId === site.get('site_id')) {
                return site;
            }
        }

        return null;
    }
}

export default SiteSelectorView;
