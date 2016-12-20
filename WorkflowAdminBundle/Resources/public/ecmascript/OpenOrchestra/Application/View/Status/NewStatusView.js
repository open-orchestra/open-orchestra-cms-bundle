import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Status           from '../../Model/Status/Status'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class NewStatusView
 */
class NewStatusView extends AbstractFormView
{
    /**
     * Render view
     */
    render() {
        let template = this._renderTemplate('Status/newStatusView', {messages: FlashMessageBag.getMessages()});
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
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

export default NewStatusView;
