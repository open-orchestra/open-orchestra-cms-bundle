import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Status               from '../../Model/Status/Status'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'
import FlashMessageBag      from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage         from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class StatusFormView
 */
class StatusFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} name
     * @param {String} statusId

     */
    initialize({form, name, statusId = null}) {
        super.initialize({form : form});
        this._statusId = statusId;
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Status/statusFormView', {
            name: this._name
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
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
        let statusId = jqXHR.getResponseHeader('statusId');
        let name = jqXHR.getResponseHeader('name');
        if (null === statusId || null === name) {
            throw new ApplicationError('Invalid statusId or name');
        }
        let url = Backbone.history.generateUrl('editStatus', {
            statusId: statusId,
            name: name
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete a status
     */
    _deleteElement() {
        if (null === this._statusId) {
            throw new ApplicationError('Invalid statusId');
        }
        let status = new Status({'id': this._statusId});
        status.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listStatus');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default StatusFormView;
