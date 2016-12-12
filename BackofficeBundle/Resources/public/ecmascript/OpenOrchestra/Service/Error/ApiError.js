import AjaxError from './AjaxError'

/**
 * @class ApiError
 */
class ApiError extends AjaxError
{
    /**
     * @param message
     */
    constructor(message){
        super(message);
        this.name = "ApiError";
    }
}

export default ApiError;
