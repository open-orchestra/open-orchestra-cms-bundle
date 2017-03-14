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
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.submit-form'] = '_refreshForm';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {String} groupId
     */
    initialize({form, groupId = null}) {
        super.initialize({form : form});
        this._groupId = groupId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = $('#oo_group_name', this._form.$form).val();
        if (null === this._groupId) {
            title = Translator.trans('open_orchestra_group.table.groups.new');
        }
        let template = this._renderTemplate('Group/groupFormView', {
            title: title
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
        let url = Backbone.history.generateUrl('editGroup', {
            groupId: groupId
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

    /**
     * Refresh form
     * @param {object} event
     */
    _refreshForm(event) {
        event.preventDefault();
        $('.group-user:checked', this.$el).each(function() {
            let container = $(this).parents('tr').eq(0);
            $('.hide', container).remove();
        });
        this._form.submit(this.getStatusCodeForm(event));
    }
}

export default GroupFormView;
