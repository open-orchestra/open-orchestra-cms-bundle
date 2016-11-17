/**
 * @class Context
 */
class Context
{
    /**
     * Constructor
     * @param {String} siteId
     * @param {String} language
     * @param {Object} user
     * @param {Array}  siteLanguages
     */
    constructor({siteId, language, user, siteLanguages}) {
        this.siteId = siteId;
        this.language = language;
        this.user = user;
        this.siteLanguages = siteLanguages;
    }
}

export default Context;
