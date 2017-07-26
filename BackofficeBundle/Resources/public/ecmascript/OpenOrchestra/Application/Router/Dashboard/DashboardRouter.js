import OrchestraRouter from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application     from 'OpenOrchestra/Application/Application'
import DashboardView   from 'OpenOrchestra/Application/View/Dashboard/DashboardView'

/**
 * @class DashboardRouter
 */
class DashboardRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'dashboard': 'showDashboard',
            '*path': 'showDashboard'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {label: Translator.trans('open_orchestra_backoffice.menu.dashboard.title')}
        ]
    }

    /**
     * showDashboard
     */
    showDashboard() {
        let dashboardView = new DashboardView();
        Application.getRegion('content').html(dashboardView.render().$el);
    }
}

export default DashboardRouter;
