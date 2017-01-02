import OrchestraRouter            from '../OrchestraRouter'
import Application                from '../../Application'
import WorkflowProfiles           from '../../Collection/WorkflowProfiles/WorkflowProfiles'
import WorkflowProfilesView       from '../../View/WorkflowProfile/WorkflowProfilesView'
import FormBuilder                from '../../../Service/Form/Model/FormBuilder'
import NewWorkflowProfileFormView from '../../View/WorkflowProfile/NewWorkflowProfileFormView'
import WorkflowProfileFormView    from '../../View/WorkflowProfile/WorkflowProfileFormView'

/**
 * @class WorkflowProfileRouter
 */
class WorkflowProfileRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'workflow-profile/list(/:page)': 'listWorkflowProfile',
            'workflow-profile/new': 'newWorkflowProfile',
            'workflow-profile/edit/:workflowProfileId': 'editWorkflowProfile'
        };
    }

    /**
     *  List WorkflowProfile
     *
     * @param {String} page
     */
    listWorkflowProfile(page) {
        if (null === page) {
            page = 1
        }
        this._diplayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new WorkflowProfiles().fetch({
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (workflowProfiles) => {
                let workflowProfilesView = new WorkflowProfilesView({
                    collection: workflowProfiles,
                    settings: {
                        page: page,
                        deferLoading: [workflowProfiles.recordsTotal, workflowProfiles.recordsFiltered],
                        data: workflowProfiles.models,
                        pageLength: pageLength
                    }
                });
                let el = workflowProfilesView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }

    /**
     * New WorkflowProfile
     */
    newWorkflowProfile() {
        let url = Routing.generate('open_orchestra_workflow_admin_workflow_profile_new');
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let newWorkflowProfileFormView = new NewWorkflowProfileFormView({form: form});
            Application.getRegion('content').html(newWorkflowProfileFormView.render().$el);
        });
    }

    /**
     * Edit WorkflowProfile
     *
     * @param  {String} workflowProfileId
     */
    editWorkflowProfile(workflowProfileId) {
        let url = Routing.generate('open_orchestra_workflow_admin_workflow_profile_form', {workflowProfileId: workflowProfileId});
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let workflowProfileFormView = new WorkflowProfileFormView({form: form});
            Application.getRegion('content').html(workflowProfileFormView.render().$el);
        });
    }
}

export default WorkflowProfileRouter;
