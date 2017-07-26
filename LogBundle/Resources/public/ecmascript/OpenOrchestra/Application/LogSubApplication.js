import LogRouter from 'OpenOrchestra/Application/Router/Log/LogRouter'

/**
 * @class LogSubApplication
 */
class LogSubApplication
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
        new LogRouter();
    }
}

export default (new LogSubApplication);
