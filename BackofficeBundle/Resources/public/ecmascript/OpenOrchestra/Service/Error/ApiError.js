import AjaxError from 'OpenOrchestra/Service/Error/AjaxError'

/**
 * @class ApiError
 */
class ApiError extends AjaxError
{
    /**
     * @param {int}    statusCode
     * @param {string} content
     * @param {string} message
     */
    constructor(statusCode, content, message){
        super(statusCode, content, message);
        this.name = "ApiError";
    }
}

export default ApiError;
