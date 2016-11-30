import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import NodesTree       from '../../Collection/Node/NodesTree'
import Statuses        from '../../Collection/Statuses/Statuses'
import Nodes           from '../../Collection/Node/Nodes'
import NodeNewTreeView from '../../View/Node/NodeNewTreeView'
import NodeFormView    from '../../View/Node/NodeFormView'
import NodeNewFormView from '../../View/Node/NodeNewFormView'
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
            'node/edit/:nodeId/:language(/:version)': 'editNode',
            'node/new/tree/:language/:parentId': 'newTreeNode',
            'node/new/:language/:parentId/:order': 'newNode'
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
     * Edit node
     *
     * @param {string} nodeId
     * @param {string|null} version
     * @param {string} language
     */
    editNode(nodeId, language, version = null) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_form', {
            siteId : Application.getContext().siteId,
            nodeId : nodeId,
            language: language,
            version: version
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NodeFormView({
                form : form,
                siteLanguages: Application.getContext().siteLanguages,
                siteId : Application.getContext().siteId,
                nodeId : nodeId,
                language: language
            });
            Application.getRegion('content').html(nodeFormView.render().$el);
        });
    }
    /**
     *  New tree node
     *
     * @param {string} language
     * @param {string} parentId
     */
    newTreeNode(language, parentId) {
        this._diplayLoader(Application.getRegion('content'));
        new NodesTree().fetch({
            urlParameter: {
                'language': language,
                'siteId': Application.getContext().siteId,
                'parentId': parentId
            },
            success: (nodesTree) => {
                let nodeNewTreeView = new NodeNewTreeView({
                    language: language,
                    nodesTree: nodesTree,
                    parentId: parentId
                });
                Application.getRegion('content').html(nodeNewTreeView.render().$el);
            }
        });
    }

    /**
     *  New Node
     *
     * @param {string} language
     * @param {string} parentId
     * @param {int}    order
     */
    newNode(language, parentId, order) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_new', {
            siteId : Application.getContext().siteId,
            language: language,
            parentId: parentId,
            order: order
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NodeNewFormView({
                form : form,
                siteLanguages: Application.getContext().siteLanguages,
                parentId : parentId,
                language: language,
                order: order
            });
            Application.getRegion('content').html(nodeFormView.render().$el);
        });
    }
}

export default NodeRouter;
