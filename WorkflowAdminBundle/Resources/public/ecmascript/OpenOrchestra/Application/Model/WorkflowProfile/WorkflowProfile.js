import OrchestraModel      from '../OrchestraModel'
import WorkflowTransitions from '../../Collection/WorkflowTransitions/WorkflowTransitions'

/**
 * @class WorkflowProfile
 */
class WorkflowProfile extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('transitions')) {
            response.transitions = new WorkflowTransitions(response.transitions, {parse: true});
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        urlParameter.workflowProfileId = this.get('id');
        switch (method) {
            case "delete":
                return Routing.generate('open_orchestra_api_workflow_profile_delete', urlParameter);
        }
    }
}

export default WorkflowProfile
