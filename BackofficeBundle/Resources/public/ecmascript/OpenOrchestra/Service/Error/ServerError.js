import AjaxError from './AjaxError'

/**
 * @class ServerError
 */
class ServerError extends AjaxError
{
    /**
     * @param {int}    statusCode
     * @param {string} content
     * @param {string} message
     */
    constructor(statusCode, content, message){
        super(statusCode, content, message);
        this.name = "ServerError";
    }
}

export default ServerError;
