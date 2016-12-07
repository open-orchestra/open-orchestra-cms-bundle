import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class UserFormView
 */
class UserFormView extends AbstractFormView
{
    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('User/userFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
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
