import StatusRouter          from './Router/Status/StatusRouter'
import ParameterRouter       from './Router/Parameter/ParameterRouter'
import WorkflowProfileRouter from './Router/WorkflowProfile/WorkflowProfileRouter'
import FormBehaviorManager   from '../Service/Form/Behavior/Manager'
import StatusParameter       from '../Service/Form/Behavior/StatusParameter'

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
        this._initFormBehaviorManager();
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new StatusRouter();
        new ParameterRouter();
        new WorkflowProfileRouter();
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(StatusParameter);
    }
}

export default (new WorkflowSubApplication);
