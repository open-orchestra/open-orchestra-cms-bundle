import DataTableCollection from '../../../Service/DataTable/Collection/DataTableCollection'
import WorkflowTransition  from '../../Model/WorkflowTransition/WorkflowTransition'

/**
 * @class WorkflowTransitions
 */
class WorkflowTransitions extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = WorkflowTransition;
    }
}

export default WorkflowTransitions
