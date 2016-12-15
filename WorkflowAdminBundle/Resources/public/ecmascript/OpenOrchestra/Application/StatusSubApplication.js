import StatusRouter        from './Router/Status/StatusRouter'
import FormBehaviorManager from '../Service/Form/Behavior/Manager'
import GroupTable          from '../Service/Form/Behavior/GroupTable'

/**
 * @class StatusSubApplication
 */
class StatusSubApplication
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
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(GroupTable);
    }
}

export default (new StatusSubApplication);
