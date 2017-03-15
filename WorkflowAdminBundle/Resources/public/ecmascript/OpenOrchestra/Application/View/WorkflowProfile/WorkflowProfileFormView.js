import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import WorkflowProfile      from '../../Model/WorkflowProfile/WorkflowProfile'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class WorkflowProfileFormView
 */
class WorkflowProfileFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} workflowProfileId
     */
    initialize({form, name, workflowProfileId = null}) {
        super.initialize({form : form});
        this._workflowProfileId = workflowProfileId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = $("input[id*='oo_workflow_profile_labels_']", this._form.$form).val();
        if (null === this._workflowProfileId) {
            title = Translator.trans('open_orchestra_workflow_admin.workflow_profile.title_new');
        }
        let template = this._renderTemplate('WorkflowProfile/workflowProfileFormView', {
            title: title
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * Redirect to edit workflow profile view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let workflowProfileId = jqXHR.getResponseHeader('workflowProfileId');
        let url = Backbone.history.generateUrl('editWorkflowProfile', {
            workflowProfileId: workflowProfileId
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
    
    /**
     * Delete workflow profile
     */
    _deleteElement() {
        if (null === this._workflowProfileId) {
            throw new ApplicationError('Invalid workflowProfileId');
        }
        let workflowProfile = new WorkflowProfile({'id': this._workflowProfileId});
        workflowProfile.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listWorkflowProfile');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default WorkflowProfileFormView;
