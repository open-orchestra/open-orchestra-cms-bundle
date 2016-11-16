import OrchestraView from '../OrchestraView'

/**
 * @class NodeTreeView
 */
class NodeTreeView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
    }

    /**
     * Initialize
     * @param {NodeTree} nodeTree
     */
    initialize({nodeTree}) {
        this._nodeTree = nodeTree;
    }

    /**
     * Render node tree
     */
    render() {
        console.log(this.nodeTree);
        let template = this._renderTemplate('Node/nodeTreeView',
            {
                'nodeTree' : this._nodeTree
            }
        );
        this.$el.html(template);

        return this;
    }
}

export default NodeTreeView;
