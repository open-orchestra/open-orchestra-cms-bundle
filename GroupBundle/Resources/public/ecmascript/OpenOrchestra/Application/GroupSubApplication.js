import GroupRouter       from './Router/Group/GroupRouter'
import FormBehaviorManager  from '../Service/Form/Behavior/Manager'
import HierarchicalCheck from '../Service/Form/Behavior/HierarchicalCheck'

/**
 * @class GroupSubApplication
 */
class GroupSubApplication
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
        new GroupRouter();
    }

    /**
    * Initialize form behavior library
    * @private
    */
   _initFormBehaviorManager() {
       FormBehaviorManager.add(HierarchicalCheck);
   }
}

export default (new GroupSubApplication);
