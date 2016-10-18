import Form        from './Form'
import ServerError from '../Error/ServerError'

/**
 * @class FormBuilder
 */
class FormBuilder
{
    /**
     * Create a form
     *
     * @param html
     * @param method
     * @param url
     */
    static createForm(html, method, url) {
        return new Form(html, method, url);
    }

    /**
     *
     * @param {string} url
     * @param {Function} callbackCreate
     * @param {Function} success
     * @param {Function} error
     */
    static createFormFromUrl(url, callbackCreate, success = null, error = null) {
        error = error || FormBuilder._errorGetForm;
        success = success || FormBuilder._successGetForm;
        Backbone.ajax({method: 'GET',url: url}).done(success).fail(error);
    }

    /**
     * @param {object} response
     * @private
     */
    static _errorGetForm(response) {
        let error = new ServerError(response.status, response.responseText, response.statusText);
        Backbone.Events.trigger('application:error', error);
    }

    static _successGetForm(data) {
        let $form = $(data);
        let method = $(data).attr('method');
        let action = $(data).attr('data-action');


    }
}

export default FormBuilder