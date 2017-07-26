import GroupRouter         from 'OpenOrchestra/Application/Router/Group/GroupRouter'
import FormBehaviorManager from 'OpenOrchestra/Service/Form/Behavior/Manager'
import HierarchicalCheck   from 'OpenOrchestra/Service/Form/Behavior/HierarchicalCheck'
import TreeCheck           from 'OpenOrchestra/Service/Form/Behavior/TreeCheck'

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
       FormBehaviorManager.add(TreeCheck);
   }
}

export default (new GroupSubApplication);
