import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import NodesTree       from '../../Collection/Node/NodesTree'
import Statuses        from '../../Collection/Status/Statuses'
import Nodes           from '../../Collection/Node/Nodes'
import NewNodeTreeView from '../../View/Node/NewNodeTreeView'
import NodeFormView    from '../../View/Node/NodeFormView'
import NewNodeFormView from '../../View/Node/NewNodeFormView'
import NodesView       from '../../View/Node/NodesView'
import Node            from '../../Model/Node/Node'
import NodeView        from '../../View/Node/NodeView'

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
            'nodes(/:language)'                     : 'showNodes',
            'node/edit/:nodeId/:language(/:version)': 'editNode',
            'node/new/tree/:language/:parentId'     : 'newTreeNode',
            'node/new/:language/:parentId/:order'   : 'newNode',
            'node/:nodeId/:language(/:version)'     : 'showNode'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.node'),
                link: '#'+Backbone.history.generateUrl('showNodes')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            showNodes   : 'navigation-node',
            editNode    : 'navigation-node',
            newTreeNode : 'navigation-node',
            newNode     : 'navigation-node',
            showNode    : 'navigation-node'
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

        this._displayLoader(Application.getRegion('content'));
        new Statuses().fetch({
            apiContext: 'nodes',
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
        this._displayLoader(Application.getRegion('content'));
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
                language: language,
                version: version
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
        this._displayLoader(Application.getRegion('content'));
        new NodesTree().fetch({
            urlParameter: {
                'language': language,
                'siteId': Application.getContext().siteId,
                'parentId': parentId
            },
            success: (nodesTree) => {
                let newNodeTreeView = new NewNodeTreeView({
                    language: language,
                    nodesTree: nodesTree,
                    parentId: parentId
                });
                Application.getRegion('content').html(newNodeTreeView.render().$el);
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
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_node_new', {
            siteId : Application.getContext().siteId,
            language: language,
            parentId: parentId,
            order: order
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let nodeFormView = new NewNodeFormView({
                form : form,
                siteLanguages: Application.getContext().siteLanguages,
                parentId : parentId,
                language: language,
                order: order
            });
            Application.getRegion('content').html(nodeFormView.render().$el);
        });
    }

    /**
     * @param {string}   nodeId
     * @param {string}   language
     * @param {int|null} version
     */
    showNode(nodeId, language, version = null) {
        this._displayLoader(Application.getRegion('content'));
        let node = new Node();
        node.fetch({
            urlParameter: {
                'language': language,
                'nodeId': nodeId,
                'siteId': Application.getContext().siteId,
                'version': version
            },
            success: () => {
                let nodeView = new NodeView({
                    node: node,
                    siteLanguages: Application.getContext().siteLanguages
                });
                Application.getRegion('content').html(nodeView.render().$el);
            }
        });
    }
}

export default NodeRouter;
