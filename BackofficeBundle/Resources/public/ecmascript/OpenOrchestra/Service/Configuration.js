/**
 * @class Configuration
 */
class Configuration
{
    /**
     * Constructor
     * @param {object} parameters
     */
    constructor(parameters = {}) {
        this._parameters = parameters;
    }

    /**
     * @param {String} name
     *
     * @return {mixed}
     */
    getParameter(name) {
        return this._parameters[name];
    }

    /**
     * @return {object}
     */
    getParameters() {
        return this._parameters;
    }

    /**
     * @param {String} name
     */
    addParameter(name, value) {
        this._parameters[name] = value;
    }
}

export default Configuration;
