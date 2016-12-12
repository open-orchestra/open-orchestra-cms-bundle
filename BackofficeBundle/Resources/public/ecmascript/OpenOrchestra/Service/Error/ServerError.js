import AjaxError from './AjaxError'

/**
 * @class ServerError
 */
class ServerError extends AjaxError
{
    /**
     * @param message
     */
    constructor(message){
        super(message);
        this.name = "ServerError";
    }
}

export default ServerError;
