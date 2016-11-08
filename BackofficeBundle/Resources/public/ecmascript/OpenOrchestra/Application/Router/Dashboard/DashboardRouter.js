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
            // '*path': 'showDashboard', -- Remove comment when refacto es6 is done
            'dashboard': 'showDashboard'
        };
    }

    /**
     * showDashboard
     */
    showDashboard() {
        let dashboardView = new DashboardView();
        console.log('dashboard');
        app.getRegion('content').html(dashboardView.render().$el);
    }
}

export default DashboardRouter;
