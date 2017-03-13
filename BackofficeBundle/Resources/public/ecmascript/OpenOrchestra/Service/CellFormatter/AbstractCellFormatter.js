/**
 * @class AbstractCellFormatter
 */
class AbstractCellFormatter
{

    /**
     * return data formatter
     *
     * @return Object
     */
    getDataFormatter() {
        throw new Error('Missing getDataFormatter method');
    }

    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return this.getDataFormatter().getType();
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
        return function(td, cellData, rowData) {
            $(td).html(this.getDataFormatter().format(cellData));
        };
    }
}

export default AbstractCellFormatter;
