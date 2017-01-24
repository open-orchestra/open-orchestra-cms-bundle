import AbstractCellFormatter from './AbstractCellFormatter'

/**
 * @class Manager
 */
class Manager
{
    /**
     * Constructor
     */
    constructor() {
        this._cellFormatters = [];
    }

    /**
     * @param {Object} cellFormatter
     */
    add(cellFormatter) {
        if (!(cellFormatter instanceof AbstractCellFormatter)) {
            throw TypeError("Manager accept only instance of AbstractCellFormatter");
        }
        this._cellFormatters.push(cellFormatter);
    }

    /**
     * get cell formatter
     *
     * @param {Object} field
     */
    format(field) {
        for (let cellFormatter of this._cellFormatters) {
            if (cellFormatter.support(field)) {
                return cellFormatter.format(field);
            }
        }
        
        return undefined;
    }
}

// unique instance of Manager
export default (new Manager);
