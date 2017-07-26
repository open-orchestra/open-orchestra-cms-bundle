import OrchestraApiSyncMixin from 'OpenOrchestra/Service/Sync/OrchestraApiMixinSync'

/**
 * @class OrchestraCollection
 */
class OrchestraCollection extends mix(Backbone.Collection).with(OrchestraApiSyncMixin)
{
    /**
     * Constructor
     */
    constructor(attributes, options) {
        super(attributes, options);
        this.bind('error', this.syncError);
    }

    /**
     * Remove multiple model in the collections
     * And on the server
     *
     * @param {array}  models
     * @param {Object} options
     */
    destroyModels(models, options = {}) {
        if (0 !== models.length) {
            let removeCollection = new this.constructor(models, {
                model: this.model,
                comparator: this.comparator
            });
            this.remove(models);

            options.data = JSON.stringify(removeCollection.toJSON());
            removeCollection.sync('delete', removeCollection, options);
        }
    }

    /**
     * @param {Object} options
     */
    save(options) {
        this.sync('update', this, options);
    }
}

export default OrchestraCollection;
