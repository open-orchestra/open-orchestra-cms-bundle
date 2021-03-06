import AbstractWorkflowRouter  from 'OpenOrchestra/Application/Router/AbstractWorkflowRouter'
import Application             from 'OpenOrchestra/Application/Application'
import WorkflowProfiles        from 'OpenOrchestra/Application/Collection/WorkflowProfiles/WorkflowProfiles'
import WorkflowProfilesView    from 'OpenOrchestra/Application/View/WorkflowProfile/WorkflowProfilesView'
import FormBuilder             from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import WorkflowProfileFormView from 'OpenOrchestra/Application/View/WorkflowProfile/WorkflowProfileFormView'

/**
 * @class WorkflowProfileRouter
 */
class WorkflowProfileRouter extends AbstractWorkflowRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'workflow/profile/list(/:page)'           : 'listWorkflowProfile',
            'workflow/profile/new'                    : 'newWorkflowProfile',
            'workflow/profile/edit/:workflowProfileId': 'editWorkflowProfile'
        };
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-workflow'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumbHighlight() {
        return {
            '*' : 'navigation-profile'
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
        this._displayLoader(Application.getRegion('content'));
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
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
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let workflowProfileFormView = new WorkflowProfileFormView({
                form: form
            });
            Application.getRegion('content').html(workflowProfileFormView.render().$el);
        });
    }

    /**
     * Edit WorkflowProfile
     *
     * @param  {String} workflowProfileId
     */
    editWorkflowProfile(workflowProfileId) {
        let url = Routing.generate('open_orchestra_workflow_admin_workflow_profile_form', {workflowProfileId: workflowProfileId});
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let workflowProfileFormView = new WorkflowProfileFormView({
                form: form,
                workflowProfileId: workflowProfileId
            });
            Application.getRegion('content').html(workflowProfileFormView.render().$el);
        });
    }
}

export default WorkflowProfileRouter;
