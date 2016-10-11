import OrchestraView from '../OrchestraView'

/**
 * @class ErrorView
 */
class ErrorView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
    }

    /**
     * Initialize
     * @param {Error} error
     */
    initialize({error}) {
        this._error = error;
    }

    /**
     * Render node tree
     */
    render() {
        this.$el = $('<p>' + this._error.message + '</p>');

        return this;
    }
}

export default ErrorView;
