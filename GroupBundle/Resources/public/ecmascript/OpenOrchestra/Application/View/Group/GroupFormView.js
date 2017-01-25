import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Group                from '../../Model/Group/Group'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class GroupFormView
 */
class GroupFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {Array}  name
     * @param {String} groupId
     */
    initialize({form, name, groupId = null}) {
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
     * Redirect to edit group view
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
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete
     */
    _deleteElement() {
        if (null === this._groupId) {
            throw new ApplicationError('Invalid groupId');
        }
        let group = new Group({'id': this._groupId});
        group.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listGroup');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default GroupFormView;
