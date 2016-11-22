import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class NodeFormView
 */
class NodeFormView extends AbstractFormView
{
    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }
}

export default NodeFormView;
