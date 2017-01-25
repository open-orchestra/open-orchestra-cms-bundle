import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class RedirectionFormView
 */
class RedirectionFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
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
     * Initialize
     * @param {Form}   form
     * @param {String} statusId
     * @param {String}  name
     */
    initialize({form, statusId, name}) {
        super.initialize({form : form});
        this._redirectionId = redirectionId;
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
     * Redirect to edit status view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let statusId = jqXHR.getResponseHeader('redirectionId');
        if (null === redirectionId) {
            throw new ApplicationError('Invalid redirectionId');
        }
        let url = Backbone.history.generateUrl('editRedirection', {
            redirectionId: RedirectionId
        });

        super._redirectEditElement(data);
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let redirection = new Redirection({'redirection_id': this._redirectionId});
        redirection.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listRedirection');
                Backbone.history.navigate(url, true);
            }
        });
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
        $('#select2-chosen-1', this.$el).html('');
    }
}

export default RedirectionFormView;
