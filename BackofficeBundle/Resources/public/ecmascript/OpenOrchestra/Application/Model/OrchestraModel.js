import OrchestraApiSyncMixin from 'OpenOrchestra/Service/Sync/OrchestraApiMixinSync'

/**
 * @class OrchestraModel
 */
class OrchestraModel extends mix(Backbone.Model).with(OrchestraApiSyncMixin)
{
    /**
     * Constructor
     */
    constructor(attributes, options) {
        super(attributes, options);
        this.on('error', this.syncError);
    }
}

export default OrchestraModel;

