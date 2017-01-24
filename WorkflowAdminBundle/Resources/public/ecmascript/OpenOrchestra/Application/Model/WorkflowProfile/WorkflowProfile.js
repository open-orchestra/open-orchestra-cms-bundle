import OrchestraModel from '../OrchestraModel'

/**
 * @class WorkflowProfile
 */
class WorkflowProfile extends OrchestraModel
{
    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.workflowProfileId = this.get('workflow_profile_id');
        switch (method) {
            case "delete":
                return Routing.generate('open_orchestra_api_workflow_profile_delete', urlParameter);
        }
    }
}

export default WorkflowProfile
