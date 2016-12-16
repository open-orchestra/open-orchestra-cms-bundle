import Form        from './Form'
import ServerError from '../../Error/ServerError'

/**
 * @class FormBuilder
 */
class FormBuilder
{
    /**
     * Create a form
     *
     * @param {string} html - form html
     */
    static createForm(html) {
        return new Form(html);
    }

    /**
     *
     * @param {string} url
     * @param {Function} callbackCreate
     * @param {Object}   data
     * @param {Function} done
     * @param {Function} error
     */
    static createFormFromUrl(url, callbackCreate, data = null, done = null, error = null) {
        error = error || FormBuilder._errorGetForm;
        done = done || FormBuilder._successGetForm;
        let promise = Backbone.ajax({method: 'GET', url: url, data: data});
            promise.fail(error);
            promise.done((data) => done(data, callbackCreate));
    }

    /**
     * @param {object} response
     * @private
     */
    static _errorGetForm(response) {
        let error = new ServerError(response.status, response.responseText, response.statusText);
        Backbone.Events.trigger('application:error', error);
    }

    /**
     * @param {string}   data
     * @param {Function} callbackCreate
     * @private
     */
    static _successGetForm(data, callbackCreate) {
        let form = FormBuilder.createForm(data);

        callbackCreate(form);
    }
}

export default FormBuilder
