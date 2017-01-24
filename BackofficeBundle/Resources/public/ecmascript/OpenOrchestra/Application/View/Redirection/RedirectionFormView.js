import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class RedirectionFormView
 */
class RedirectionFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click :radio'] = '_clickRadio';
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Redirection/redirectionFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        this._switchType($("input[name='oo_redirection[type]']:checked", this.$el).val());

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

    /**
     * @param {Object} event
     */
    _clickRadio(event) {
        this._switchType($(event.currentTarget).val());
    }

    /**
     * @param {Object} event
     */
    _switchType(selectedValue) {
        if ('internal' == selectedValue) {
            this._show_internal();
        } else if ('external' == selectedValue) {
            this._show_external();
        }
    }

    /**
     * Show internal type form
     */
    _show_internal() {
        $('#oo_redirection_url', this.$el).closest('div.form-group').hide();
        $('#oo_redirection_nodeId', this.$el).closest('div.form-group').show();
        $('#oo_redirection_url', this.$el).val('');
    }

    /**
     * Show external type form
     */
    _show_external() {
        $('#oo_redirection_nodeId', this.$el).closest('div.form-group').hide();
        $('#oo_redirection_url', this.$el).closest('div.form-group').show();
        $('#oo_redirection_nodeId', this.$el).val('');
    }
}

export default RedirectionFormView;
