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

    /**
     * @inheritDoc
     */
    destroyModels(models, options = {}) {
        super.destroyModels(models, options);
        console.log(this.length);
        this.recordsTotal = this.length;
    }
}

export default DataTableCollection
