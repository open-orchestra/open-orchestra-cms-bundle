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
        let view = this;
        this._renderTemplate('openorchestrabackoffice/underscore/Node/nodeTreeView',
            {'nodeTree' : this._nodeTree},
            (template) => {
                view.$el.append(template);
            }
        );

        return this;
    }
}

export default NodeTreeView;
