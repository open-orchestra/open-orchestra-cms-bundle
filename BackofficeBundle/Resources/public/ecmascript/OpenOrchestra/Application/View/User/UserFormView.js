import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class UserFormView
 */
class UserFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form} form
     */
    initialize({form, userId}) {
        this._form = form;
        this._userId = false;
        if(typeof userId != 'undefined') {
            this._userId = userId;
        }
    }

    getUserId() {
        return this._userId;
    }
    
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

export default UserFormView;
