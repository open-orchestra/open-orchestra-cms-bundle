import FlashMessage from 'OpenOrchestra/Service/FlashMessage/FlashMessage'

/**
 * @class FlashMessageBag
 */
class FlashMessageBag
{
    /**
     * Constructor
     */
    constructor() {
        this._messages = [];
    }

    /**
     * @param {FlashMessage} messageFlash
     *
     * @return {mixed}
     */
    addMessageFlash(messageFlash) {
        if (messageFlash instanceof FlashMessage) {
            this._messages.push(messageFlash);
        }
    }

    /**
     * @return {Array}
     */
    getMessages() {
        let messages = this._messages;
        this._messages = [];

        return messages;
    }
}

export default (new FlashMessageBag);
