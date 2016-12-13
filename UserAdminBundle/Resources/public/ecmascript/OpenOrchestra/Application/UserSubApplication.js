import UserRouter           from './Router/User/UserRouter'
import GroupRouter          from './Router/Group/GroupRouter'
import FormBehaviorManager  from '../Service/Form/Behavior/Manager'
import GroupTable           from '../Service/Form/Behavior/GroupTable'

/**
 * @class UserSubApplication
 */
class UserSubApplication
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
        new UserRouter();
        new GroupRouter();
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(GroupTable);
    }

}

export default (new UserSubApplication);
