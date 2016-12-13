import GroupRouter from './Router/Group/GroupRouter'

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
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new GroupRouter();
    }
}

export default (new GroupSubApplication);
