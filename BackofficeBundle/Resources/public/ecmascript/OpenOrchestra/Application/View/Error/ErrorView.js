import ModalView     from '../../../Service/Modal/View/ModalView'

/**
 * @class ErrorView
 */
class ErrorView extends ModalView
{
    /**
     * Initialize
     *
     * @param {Error} error
     * @param {String} type
     */
    initialize({error, type}) {
        this._error = error;
        this._type = type;
    }

    /**
     * Render error
     */
    render() {
        let template = this._renderTemplate('Error/errorModalView', {
            error: this._error,
            type: this._type
        });
        this.$el.html(template);

        return this;
    }
}

export default ErrorView;
