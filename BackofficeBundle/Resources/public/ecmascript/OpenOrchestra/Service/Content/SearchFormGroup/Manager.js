import AbstractSearchFormGroup from './AbstractSearchFormGroup'

/**
 * @class Manager
 */
class Manager
{
    /**
     * Constructor
     */
    constructor() {
        this._fieldSearchs = [];
    }

    /**
     * @param {Object} fieldSearch
     */
    add(fieldSearch) {
        if (!(fieldSearch instanceof AbstractSearchFormGroup)) {
            throw TypeError("Manager accept only instance of AbstractSearchFormGroup");
        }
        this._fieldSearchs.push(fieldSearch);
    }

    /**
     * render field search
     *
     * @param {Object} field
     */
    render(field) {
        for (let fieldSearch of this._fieldSearchs) {
            if (fieldSearch.support(field)) {
                return fieldSearch.render(field);
            }
        }
        
        throw new Error('No rendering founded for search of type ' + field.type + ' define in field ' + field.label);
    }
}

// unique instance of Manager
export default (new Manager);
