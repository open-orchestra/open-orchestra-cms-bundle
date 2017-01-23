import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import WorkflowProfile      from '../../Model/WorkflowProfile/WorkflowProfile'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'

/**
 * @class WorkflowProfileFormView
 */
class WorkflowProfileFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} workflowProfileId
     * @param {Array}  name
     */
    initialize({form, workflowProfileId, name}) {
        super.initialize({form : form});
        this._workflowProfileId = workflowProfileId;
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('WorkflowProfile/workflowProfileFormView', {
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
        let workflowProfileId = jqXHR.getResponseHeader('workflowProfileId');
        let name = jqXHR.getResponseHeader('name');
        if (null === workflowProfileId || null === name) {
            throw new ApplicationError('Invalid workflowProfileId or name');
        }
        let url = Backbone.history.generateUrl('editWorkflowProfile', {
            workflowProfileId: workflowProfileId,
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
        let workflowProfile = new WorkflowProfile({'workflow_profile_id': this._workflowProfileId});
        workflowProfile.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listWorkflowProfile');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default WorkflowProfileFormView;
