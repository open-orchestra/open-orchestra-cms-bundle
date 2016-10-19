import FormView from '../Form/FormView'

/**
 * @class NodeFormView
 */
class NodeFormView extends FormView
{
    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '400': $.proxy(this.refreshRender, this)
        }
    }
}

export default NodeFormView;
