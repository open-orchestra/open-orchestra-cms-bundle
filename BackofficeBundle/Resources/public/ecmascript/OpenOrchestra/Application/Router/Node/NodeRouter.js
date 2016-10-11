import OrchestraRouter from '../OrchestraRouter'

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
            'node/tree': 'nodeTree'
        };
    }

    /**
     * Initialize router
     * @param {NodeController} nodeController
     */
    initialize({nodeController}) {
        this._nodeController = nodeController
    }

    /**
     * Show node tree
     */
    nodeTree() {
        this._nodeController.showNodeTreeAction()
    }
}

export default NodeRouter;
