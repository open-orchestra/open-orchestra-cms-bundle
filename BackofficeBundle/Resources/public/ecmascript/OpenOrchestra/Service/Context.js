/**
 * @class Context
 */
class Context
{
    /**
     * Constructor
     * @param {Object} routing
     * @param {String} siteId
     * @param {String} language
     * @param {Object} user
     * @param {Array}  siteLanguages
     */
    constructor({routing, siteId, language, user, siteLanguages}) {
        this.routing = routing;
        this.siteId = siteId;
        this.language = language;
        this.user = user;
        this.siteLanguages = siteLanguages;
    }

    /**
     * Reset context on server and reload pages
     */
    refreshContext() {
        let url = Routing.generate('clearContext');
        $.get(url).done(() => window.location.reload());
    }
}

export default Context;
