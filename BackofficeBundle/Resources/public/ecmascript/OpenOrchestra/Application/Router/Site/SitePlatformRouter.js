import SiteRouter  from './SiteRouter'

/**
 * @class SitePlatformRouter
 */
class SitePlatformRouter extends SiteRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'plateform/site/list(/:page)' : 'listPlatformSite',
            'plateform/site/edit/:siteId' : 'editPlatformSite',
        }
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.platform.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.site'),
                link: '#'+Backbone.history.generateUrl('listPlatformSite')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getNavigationHighlight() {
        return {
            listPlatformSite : 'course-platform-site',
            editPlatformSite : 'course-platform-site',
        };
    }

    /**
     * List Site
     *
     * @param {int} page
     */
    listPlatformSite(page) {
        if (null === page) {
            page = 1
        }
        super.listSite(page, true);
    }

    /**
     * Edit site
     *
     * @param {string} siteId
     */
    editPlatformSite(siteId) {
        super.editSite(siteId, true);
    }
}

export default SitePlatformRouter;
