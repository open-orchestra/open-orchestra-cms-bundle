import OrchestraRouter from '../OrchestraRouter'
import app             from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import NodeTree        from '../../Model/Node/NodeTree'
import NodeTreeView    from '../../View/Node/NodeTreeView'
import NodeFormView    from '../../View/Node/NodeFormView'

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
            'node/tree': 'showNodeTree',
            'node/new': 'newNode'
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

    /**
     * Create new node
     */
    newNode() {
        this._diplayLoader(app.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_new', {parentId : 'root'});
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NodeFormView({form : form});
            app.getRegion('content').html(nodeFormView.render().$el);
        });
    }
}

export default NodeRouter;
