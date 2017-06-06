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

    /**
     * Update user access rights
     * @param {String} section
     * @param {String} access
     */
    updateUserAccessSection(section, access) {
        if (typeof this.user !== 'undefined' && typeof this.user.access_section !== 'undefined') {
            this.user.access_section[section] = access;
        }
    }

    /**
     * Get a context property
     * @param {String} property
     *
     * @return {mixed}
     */
    get(property) {
        if (this.hasOwnProperty(property)) {
            return this[property];
        }

        throw new Error('Missing property ' + property + ' in context');
    }

    /**
     * Set a context property
     * @param {String} property
     * @param {mixed}  value
     */
    set(property, value) {
        this[property] = value;
    }
}

export default Context;
