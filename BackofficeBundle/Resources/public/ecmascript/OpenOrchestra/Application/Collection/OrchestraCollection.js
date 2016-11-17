import ApiError    from '../../Service/Error/ApiError';
import ServerError from '../../Service/Error/ServerError';

/**
 * @class OrchestraCollection
 */
class OrchestraCollection extends Backbone.Collection
{
    /**
     * Constructor
     */
    constructor(attributes, options) {
        super(attributes, options);
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
     * @param {object} options - sync options
     *
     * @returns {Object}
     * @private
     */
    _getSyncUrl(options) {
        return {}
    }

    /**
     * @inheritdoc
     */
    sync(method, model, options = {}) {
        let url = this._getSyncUrl(options)[method.toLowerCase()];
        if (typeof url != 'undefined') {
            options.url = url;
        }

        return Backbone.sync.apply(this, [method, model, options]);
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

    save(options) {
        this.sync('update', this, options);
    }
}

export default OrchestraCollection;
