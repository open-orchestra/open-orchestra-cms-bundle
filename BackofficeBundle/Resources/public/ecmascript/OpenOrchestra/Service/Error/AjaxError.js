/**
 * @class AjaxError
 */
class AjaxError extends Error
{
    /**
     * @param {int}    statusCode
     * @param {string} content
     * @param {string} message
     */
    constructor(statusCode, content, message = '') {
        super(message);
        this.content = content;
        this.statusCode = statusCode;
        this.name = 'AjaxError';
    }
}

export default AjaxError;
