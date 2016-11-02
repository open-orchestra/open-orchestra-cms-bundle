import OrchestraCollection from '../../../Application/Collection/OrchestraCollection'

/**
 * @class DataTableCollection
 */
class DataTableCollection extends OrchestraCollection
{
    /**
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('recordsTotal')) {
            this.recordsTotal = response.recordsTotal;
        }
        if (response.hasOwnProperty('recordsFiltered')) {
            this.recordsFiltered = response.recordsFiltered;
        }
        if (response.hasOwnProperty('collection_name')) {
            return response[response.collection_name];
        }

        return response;
    }
}

export default DataTableCollection
