import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class UserFormView
 */
class UserFormView extends AbstractFormView
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

export default UserFormView;
