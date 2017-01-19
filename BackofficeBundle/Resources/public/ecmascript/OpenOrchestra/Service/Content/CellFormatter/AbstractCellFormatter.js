/**
 * @class AbstractCellFormatter
 */
class AbstractCellFormatter
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        throw new Error('Missing support method');
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    format(field) {
        throw new Error('Missing format method');
    }
}

export default AbstractCellFormatter;
