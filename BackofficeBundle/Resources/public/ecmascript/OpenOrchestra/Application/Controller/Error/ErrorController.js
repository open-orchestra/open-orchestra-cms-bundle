import app from '../../Application'
import OrchestraController from '../OrchestraController'
import ErrorView from '../../View/Error/ErrorView'
import AjaxError from '../../Error/AjaxError'

/**
 * @class ErrorController
 */
class ErrorController extends OrchestraController
{
    /**
     * Constructor
     */
    constructor() {
        super();
        Backbone.Events.bind('application:error', (error) => {
           this.showErrorAction(error);
        });
    }

    /**
     * @param {Error} error
     */
    showErrorAction(error) {
        if (error instanceof AjaxError && error.getStatusCode() === 401) {
            window.location.pathname = Routing.generate('fos_user_security_login', true);
        }
        if (app.getConfiguration().getParameter('debug')) {
            let errorView = new ErrorView({error: error});
            app.getRegion('content').html(errorView.render().$el);
        }
    }
}

export default ErrorController;