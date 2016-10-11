import ApiError from '../Error/ApiError';
import ServerError from '../Error/ServerError';

/**
 * @class OrchestraModel
 */
class OrchestraModel extends Backbone.Model
{
    /**
     * Constructor
     */
    constructor() {
        super();
        this.bind('error', this.syncError);
    }

    /**
     * Get list url by method
     * For instance
     * {
     * 'read' : '/user/get',
     * 'create': '/user/create',
     * 'update': '/user/update',
     * 'delete': '/user/remove'
     * }
     * @returns {Object}
     * @private
     */
    _getSyncUrl() {
        return {}
    }

    /**
     * @inheritdoc
     */
    sync(method, model, options) {
        options = options || {};
        let url = model._getSyncUrl()[method.toLowerCase()];
        if(typeof url != 'undefined') {
            options.url = url;
        }

        return Backbone.sync.apply(this, arguments);
    }

    /**
     * @param {OrchestraModel} model
     * @param {object}         response
     * @param {object}         options
     */
    syncError(model, response, options) {
        var error;
        if (typeof response.JSON != 'undefined') {
            error = new ApiError(response.status, response.responseJSON, response.statusText);
        } else {
            error = new ServerError(response.status, response.responseText, response.statusText)
        }

        Backbone.Events.trigger('application:error', error);
    }
}

export default OrchestraModel;
