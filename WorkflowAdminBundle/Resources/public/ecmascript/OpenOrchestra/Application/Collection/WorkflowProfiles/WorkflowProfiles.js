import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import WorkflowProfile     from '../../Model/WorkflowProfile/WorkflowProfile'

/**
 * @class WorkflowProfiles
 */
class WorkflowProfiles extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = WorkflowProfile;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'workflow_profiles': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method) {
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_workflow_profile_list');
            case "delete":
                return Routing.generate('open_orchestra_api_workflow_profile_delete_multiple');
        }
    }
}

export default WorkflowProfiles
