import ApiError              from '../Error/ApiError';
import ServerError           from '../Error/ServerError';

let OrchestraApiSyncMixin = (superclass) => class extends superclass {

    /**
     * Get url by method
     *
     * @param {string} method - (read, create, update, delete)
     * @param {object} options - sync options
     *
     * @returns {String}
     * @private
     */
    _getSyncUrl(method, options) {}

    /**
     * @see Backbone.sync
     */
    sync(method, model, options = {}) {
        let url = this._getSyncUrl(method, options);
        if (typeof url != 'undefined') {
            options.url = url;
        }

        if (typeof options.enabledCallbackError == 'undefined') {
            options.enabledCallbackError = true;
        }

        return Backbone.sync.apply(this, [method, model, options]);
    }

    /**
     * @param {OrchestraModel} model
     * @param {object}         response
     * @param {object}         options
     */
    syncError(model, response, options) {
        if (options.enabledCallbackError !== false) {
            var error;
            if (typeof response.responseJSON !== 'undefined') {
                error = new ApiError(response.status, response.responseJSON, response.statusText);
            } else {
                error = new ServerError(response.status, response.responseText, response.statusText)
            }

            Backbone.Events.trigger('application:error', error);
        }
    }
};

export default OrchestraApiSyncMixin;
