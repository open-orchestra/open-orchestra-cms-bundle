import UserRouter           from 'OpenOrchestra/Application/Router/User/UserRouter'
import FormBehaviorManager  from 'OpenOrchestra/Service/Form/Behavior/Manager'
import GroupTable           from 'OpenOrchestra/Service/Form/Behavior/GroupTable'
import UserTable           from 'OpenOrchestra/Service/Form/Behavior/UserTable'

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
