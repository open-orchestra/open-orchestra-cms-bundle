import StatusRouter           from './Router/Status/StatusRouter'
import WorkflowProfileRouter  from './Router/WorkflowProfile/WorkflowProfileRouter'

/**
 * @class WorkflowSubApplication
 */
class WorkflowSubApplication
{
    /**
     * Run sub Application
     */
    run() {
        this._initRouter();
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new StatusRouter();
        new WorkflowProfileRouter();
    }
}

export default (new WorkflowSubApplication);
