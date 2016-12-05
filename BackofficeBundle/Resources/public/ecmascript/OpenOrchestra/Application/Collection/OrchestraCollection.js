import OrchestraApiSyncMixin from '../../Service/Sync/OrchestraApiMixinSync'

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
        this.remove(models);
        options.data = JSON.stringify(this.toJSON());
        this.sync('delete', this, options);
    }

    /**
     * @param {Object} options
     */
    save(options) {
        this.sync('update', this, options);
    }
}

export default OrchestraCollection;
