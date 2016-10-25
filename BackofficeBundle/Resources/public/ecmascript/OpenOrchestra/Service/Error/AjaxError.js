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
        this._content = content;
        this._statusCode = statusCode;
    }

     /**
     * @returns {string}
     */
    getContent() {
        return this._content;
    }

    /**
     * @returns {int}
     */
    getStatusCode() {
        return this._statusCode;
    }
}

export default AjaxError;
