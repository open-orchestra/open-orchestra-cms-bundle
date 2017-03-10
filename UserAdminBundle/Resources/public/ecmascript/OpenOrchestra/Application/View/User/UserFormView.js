import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'

/**
 * @class UserFormView
 */
class UserFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form} form
     * @param {Boolean} activatePreferenceTab
     */
    initialize({form, activatePreferenceTab}) {
        super.initialize({form: form});
        this._activatePreferenceTab = activatePreferenceTab;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('User/userFormView', {messages: FlashMessageBag.getMessages()});
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritdoc
     */
    _renderForm() {
        super._renderForm();

        if (true === this._activatePreferenceTab) {
            $('.nav-tabs a.nav-tab-preference', this._$formRegion).tab('show');
            $('.tab-content .tab-pane', this._$formRegion).removeClass('active');
            $('.tab-content .tab-preference', this._$formRegion).addClass('active');
        }

        $('#page-name', this.$el).html($('#oo_user_email', this.$el).val());

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
