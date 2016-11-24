import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import NodesTree       from '../../Collection/Node/NodesTree'
import Statuses        from '../../Collection/Statuses/Statuses'
import Nodes           from '../../Collection/Node/Nodes'
import NodesTreeView   from '../../View/Node/NodesTreeView'
import NodeFormView    from '../../View/Node/NodeFormView'
import NodeListView    from '../../View/Node/NodeListView'
import NodesView       from '../../View/Node/NodesView'

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
            'nodes(/:language)': 'showNodes',
            'node/new': 'newNode'
        };
    }

    /**
     * Show nodes
     *
     * @param {string} language
     */
    showNodes(language) {
        if (null === language) {
            language = Application.getContext().user.language.contribution
        }

        this._diplayLoader(Application.getRegion('content'));
        new Statuses().fetch({
            success: (statuses) => {
                let nodesView = new NodesView({
                    statuses: statuses,
                    language: language,
                    siteLanguages: Application.getContext().siteLanguages,
                    siteId: Application.getContext().siteId
                });
                Application.getRegion('content').html(nodesView.render().$el);
            }
        });
    }

    /**
     * Create new node
     */
    newNode() {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_new', {parentId : 'root'});
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NodeFormView({form : form});
            Application.getRegion('content').html(nodeFormView.render().$el);
        });
    }
}

export default NodeRouter;
