import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import Application      from '../../../Application/Application'

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
        let template = this._renderTemplate('User/userFormView', {
            messages: FlashMessageBag.getMessages(),
            title   : $('#oo_user_firstName', this._form.$form).val() + ' ' + $('#oo_user_lastName', this._form.$form).val()
        });
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

        return this;
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': Application.getContext().refreshContext,
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }


}

export default UserFormView;
