import OrchestraRouter from '../OrchestraRouter'
import app             from '../../Application'
import DashboardView   from '../../View/Dashboard/DashboardView'

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
        app.getRegion('content').html(dashboardView.render().$el);
    }
}

export default DashboardRouter;
