/**
 * @class ApplicationError
 */
class ApplicationError extends Error
{
    /**
     * @param message
     */
    constructor(message){
        super(message);
        this.name = "ApplicationError";
    }
}

export default ApplicationError;
