import OrchestraView from '../../../Application/View/OrchestraView'

/**
 * @class AbstractFormView
 */
class AbstractFormView extends OrchestraView
{
    /**
     * Constructor
     */
    constructor (options) {
        super(options);
        if (this.constructor === AbstractFormView) {
            throw TypeError("Can not construct abstract class");
        }
    }

    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        this.events = {
            'click button.submit-form': '_submit'
        };
    }

    /**
     * Initialize
     * @param {Form} form
     */
    initialize({form}) {
        this._form = form;
        this._$formRegion = this.$el;
    }

    /**
     * Render
     */
    render() {
        this._renderForm();

        return this;
    }

    /**
     * Refresh render
     */
    refreshRender() {
        Backbone.Events.trigger('form:deactivate', this);
        this._renderForm();
    }

    /**
     * Render a form
     *
     * @private
     */
    _renderForm() {
        this._$formRegion.html('');
        for (let message of this._form.$messages) {
            this._$formRegion.append(message);
        }
        this._$formRegion.append(this._form.$form);
        console.log(this);
        Backbone.Events.trigger('form:activate', this);

        return this;
    }

    /**
     * @param  {Object} event
     *
     * @return {Object}
     */
    getStatusCodeForm(event) {}

    /**
     * Submit form
     * @param {object} event
     */
    _submit(event) {
        event.preventDefault();
        this._form.submit(this.getStatusCodeForm(event));
    }
}

export default AbstractFormView;
