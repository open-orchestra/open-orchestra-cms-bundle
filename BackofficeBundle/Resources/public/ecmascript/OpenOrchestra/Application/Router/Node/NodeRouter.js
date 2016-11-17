import OrchestraRouter from '../OrchestraRouter'
import App             from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import NodesTree       from '../../Collection/Node/NodesTree'
import NodesTreeView   from '../../View/Node/NodesTreeView'
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
            'node/tree(/:language)': 'showNodeTree',
            'node/new': 'newNode'
        };
    }

    /**
     * Show node tree
     * @param {string} language
     */
    showNodeTree(language) {
        if (null === language) {
            language = App.getContext().user.language.contribution
        }
        this._diplayLoader(App.getRegion('content'));
        new NodesTree().fetch({
            urlParameter: {
                'language': language,
                'siteId': App.getContext().siteId
            },
            success: (nodesTree) => {
                let treeView = new NodesTreeView({nodesTree : nodesTree, language: language});
                App.getRegion('content').html(treeView.render().$el);
            }
        });
    }

    /**
     * Create new node
     */
    newNode() {
        this._diplayLoader(App.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_new', {parentId : 'root'});
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NodeFormView({form : form});
            App.getRegion('content').html(nodeFormView.render().$el);
        });
    }
}

export default NodeRouter;
