import StatusRouter          from 'OpenOrchestra/Application/Router/Status/StatusRouter'
import ParameterRouter       from 'OpenOrchestra/Application/Router/Parameter/ParameterRouter'
import WorkflowProfileRouter from 'OpenOrchestra/Application/Router/WorkflowProfile/WorkflowProfileRouter'
import TransitionRouter      from 'OpenOrchestra/Application/Router/Transition/TransitionRouter'
import FormBehaviorManager   from 'OpenOrchestra/Service/Form/Behavior/Manager'
import StatusParameter       from 'OpenOrchestra/Service/Form/Behavior/StatusParameter'

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
        new TransitionRouter();
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
