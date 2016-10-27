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
        if (new.target === AbstractFormView) {
            throw TypeError("Can not construct abstract class");
        }
        super(options);
    }

    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        this.events = {
            'click button[type="submit"]': '_submit'
        };
    }

    /**
     * Initialize
     * @param {Form} form
     */
    initialize({form}) {
        this._form = form;
    }

    /**
     * Render a form
     */
    render() {
        this.$el.html('');
        for (let message of this._form.$messages) {
            this.$el.append(message);
        }
        this.$el.append(this._form.$form);

        //@todo refacto this line when form behavior is convert in ES6
        OpenOrchestra.FormBehavior.channel.trigger('activate', this, this.$el);

        return this;
    }

    /**
     * Refresh render
     */
    refreshRender() {
        //@todo refacto this line when form behavior is convert in ES6
        OpenOrchestra.FormBehavior.channel.trigger('deactivate', this, this.$el);
        this.render();
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm() {}

    /**
     * Submit form
     * @param {object} event
     */
    _submit(event) {
        if (this._form.isValid()) {
            event.preventDefault();
            this._form.submit(this.getStatusCodeForm());
        }
    }
}

export default AbstractFormView;