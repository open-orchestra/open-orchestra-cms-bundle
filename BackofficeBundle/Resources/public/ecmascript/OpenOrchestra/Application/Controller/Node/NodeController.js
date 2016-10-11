import NodeTree from '../../Model/Node/NodeTree'
import NodeTreeView from '../../View/Node/NodeTreeView'
import app from '../../Application'
import NodeRouter from '../../Router/Node/NodeRouter'
import OrchestraController from '../OrchestraController'

/**
 * @class NodeController
 */
class NodeController extends OrchestraController
{
    /**
     * Constructor
     */
    constructor() {
        super();
        new NodeRouter({
            'nodeController' : this
        });
    }

    /**
     * Show node tree action
     */
    showNodeTreeAction() {
        this._diplayLoader(app.getRegion('content'));
        new NodeTree().fetch({
            success: (nodeTree) => {
                let treeView = new NodeTreeView({nodeTree : nodeTree});
                app.getRegion('content').html(treeView.render().$el);
            }
        });
    }
}

export default NodeController;