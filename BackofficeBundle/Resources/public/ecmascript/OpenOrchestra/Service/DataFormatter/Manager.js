import AbstractDataFormatter from 'OpenOrchestra/Service/DataFormatter/AbstractDataFormatter'

/**
 * @class Manager
 */
class Manager
{
    /**
     * Constructor
     */
    constructor() {
        this._dataFormatters = [];
    }

    /**
     * @param {Object} dataFormatter
     */
    add(dataFormatter) {
        if (!(dataFormatter instanceof AbstractDataFormatter)) {
            throw TypeError("Manager accept only instance of AbstractDataFormatter");
        }
        dataFormatter.initialize();
        this._dataFormatters.push(dataFormatter);
    }

    /**
     * get cell formatter
     *
     * @param {Object} field
     */
    format(field) {
        for (let dataFormatter of this._dataFormatters) {
            if (field.type == dataFormatter.getType()) {
                return function(td, cellData, rowData) {
                    $(td).html(dataFormatter.format(cellData));
                };
            }
        }
        
        return undefined;
    }
}

// unique instance of Manager
export default (new Manager);
