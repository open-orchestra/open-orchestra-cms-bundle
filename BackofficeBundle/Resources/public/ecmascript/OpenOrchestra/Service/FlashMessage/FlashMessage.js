/**
 * @class FlashMessage
 */
class FlashMessage
{
    /**
     * Constructor
     * @param {string} message
     * @param {string} type
     */
    constructor(message, type = 'info') {
        this._message = message;
        this._type = type;
    }

    /**
     * @return {string}
     */
    getMessage() {
        return this._message;
    }

    /**
     * @return {string}
     */
    getType() {
        return this._type;
    }
}

export default FlashMessage;
