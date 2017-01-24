import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Group                from '../../Model/Group/Group'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class GroupFormView
 */
class GroupFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} groupId
     * @param {Array}  name
     */
    initialize({form, groupId, name}) {
        super.initialize({form : form});
        this._groupId = groupId;
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Group/groupFormView', {
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
        let groupId = jqXHR.getResponseHeader('groupId');
        let name = jqXHR.getResponseHeader('name');
        if (null === groupId || null === name) {
            throw new ApplicationError('Invalid groupId or name');
        }
        let url = Backbone.history.generateUrl('editGroup', {
            groupId: groupId,
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
        let group = new Group({'group_id': this._groupId});
        group.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listGroup');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default GroupFormView;
