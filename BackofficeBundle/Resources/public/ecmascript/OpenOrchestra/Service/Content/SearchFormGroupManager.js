/**
 * @class SearchFormGroupManager
 */
class SearchFormGroupManager
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

// unique instance of SearchFormGroupManager
export default (new SearchFormGroupManager);
