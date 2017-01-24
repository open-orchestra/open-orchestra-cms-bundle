import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Status               from '../../Model/Status/Status'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class StatusFormView
 */
class StatusFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} statusId
     * @param {String}  name
     */
    initialize({form, statusId, name}) {
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
     * Redirect to edit user view
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
        let message = new FlashMessage(data, 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
    
    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let status = new Status({'status_id': this._statusId});
        status.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listStatus');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default StatusFormView;
