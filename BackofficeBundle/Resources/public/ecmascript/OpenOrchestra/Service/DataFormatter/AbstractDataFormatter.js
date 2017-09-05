/**
 * @class AbstractDataFormatter
 */
class AbstractDataFormatter
{
    /**
     * Initialize
     */
    initialize() {
    }

    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        throw new Error('Missing getType method');
    }

    /**
     * render the field
     *
     * @param   {string} value
     * @returns {string}
     */
    format(value) {
        throw new Error('Missing format method');
    }
}

export default AbstractDataFormatter;
