import ServerError from '../../Error/ServerError'

/**
 * @class Form
 */
class Form
{
    /**
     * Constructor
     *
     * @param {string} $html - html form
     */
    constructor($html) {
        _.extend(this, Backbone.Events);
        this.$messages = [];
        this.$form = null;
        this.method = null;
        this.action = null;

        this._parseHtml($html);
    }

    /**
     * Valid form
     *
     * @returns {boolean}
     */
    isValid() {
        return this.$form.get(0).checkValidity();
    }

    /**
     * Submit area
     *
     * @return {Object}
     */
    submit(statusCode = {}) {
        this.trigger('form:pre_submit');
        let promise = $.ajax({
                url: this.action,
                method: this.method,
                data: this.$form.serialize(),
                statusCode: statusCode,
                context: this
            });
            promise.done(this._formSuccess);
            promise.fail(this._formError);
            promise.always(() => {
                this.trigger('form:post_submit');
            });

        return promise;
    }

    /**
     * @param {string} data
     * @private
     */
    _formSuccess(data) {
        this._parseHtml(data);
    }

   /**
     * @param {Object} response
     * @private
     */
    _formError(response) {
       if (422 === response.status) {
           this._parseHtml(response.responseText);
       } else {
           let error = new ServerError(response.status, response.responseText, response.statusText);
           Backbone.Events.trigger('application:error', error);
       }
    }

    /**
     * @param {string} html
     * @private
     */
    _parseHtml(html) {
        let $form = $('<div>').html(html);
        this.$form = $('form', $form);
        this.$messages = $('.alert', $form).toArray();
        this.method = this.$form.attr('method');
        this.action = this.$form.attr('data-action');
    }
}

export default Form;
