import StatusRouter from './Router/Status/StatusRouter'

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
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new StatusRouter();
    }
}

export default (new StatusSubApplication);
