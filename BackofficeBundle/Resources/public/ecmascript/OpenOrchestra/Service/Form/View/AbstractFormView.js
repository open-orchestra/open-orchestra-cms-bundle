import OrchestraView      from '../../../Application/View/OrchestraView'
import LoadingButtonMixin from '../Mixin/LoadingButtonMixin'

/**
 * @class AbstractFormView
 */
class AbstractFormView extends mix(OrchestraView).with(LoadingButtonMixin)
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
            'click button.submit-form': '_submit',
            'keydown form': '_preventSubmitEnter'
        };
    }

    /**
     * Initialize
     * @param {Form} form
     */
    initialize({form}) {
        this._form = form;
        this._$formRegion = this.$el;
        this._form.on('form:post_submit', $.proxy(this._resetSubmitButton, this));
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
        this.activateLoading($(event.currentTarget));
        this._form.submit(this.getStatusCodeForm(event));
    }

    /**
     * Reset laading submit button
     * @private
     */
    _resetSubmitButton() {
        this.resetLoadingButton($('button.submit-form', this.$el));
    }

    /**
     * @param {Object} event
     * @private
     */
    _preventSubmitEnter(event) {
        if (13 === event.keyCode)  {
            event.preventDefault();
            return false;
        }
    }

    /**
     * @return {Form}
     */
    getForm() {
        return this._form;
    }
}

export default AbstractFormView;
