import UserRouter           from './Router/User/UserRouter'
import FormBehaviorManager  from '../Service/Form/Behavior/Manager'
import GroupTable           from '../Service/Form/Behavior/GroupTable'
import UserTable           from '../Service/Form/Behavior/UserTable'

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
    }

    /**
     * Initialize form behavior library
     * @private
     */
    _initFormBehaviorManager() {
        FormBehaviorManager.add(GroupTable);
        FormBehaviorManager.add(UserTable);
    }

}

export default (new UserSubApplication);
