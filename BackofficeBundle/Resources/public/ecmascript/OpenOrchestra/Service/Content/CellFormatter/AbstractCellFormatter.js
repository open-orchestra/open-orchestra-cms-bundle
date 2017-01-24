/**
 * @class AbstractCellFormatter
 */
class AbstractCellFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        throw new Error('Missing getType method');
    }

    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        return field.type == this.getType();
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
