/**
 * @class Context
 */
class Context
{
    /**
     * Constructor
     * @param {String} siteId
     * @param {String} language
     */
    constructor({siteId, language, user}) {
        this.siteId = siteId;
        this.language = language;
        this.user = user;
    }
}

export default Context;
