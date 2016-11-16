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
     * @param {Object} options
     */
    save(options) {
        this.sync('update', this, options);
    }
}

export default OrchestraCollection;
