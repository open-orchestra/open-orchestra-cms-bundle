import OrchestraRouter from '../OrchestraRouter'
import NodeTree        from '../../Model/Node/NodeTree'
import NodeTreeView    from '../../View/Node/NodeTreeView'
import app             from '../../Application'

/**
 * @class NodeRouter
 */
class NodeRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'node/tree': 'showNodeTree'
        };
    }

    /**
     * Show node tree
     */
    showNodeTree() {
        this._diplayLoader(app.getRegion('content'));
        new NodeTree().fetch({
            success: (nodeTree) => {
                let treeView = new NodeTreeView({nodeTree : nodeTree});
                app.getRegion('content').html(treeView.render().$el);
            }
        });
    }
}

export default NodeRouter;
