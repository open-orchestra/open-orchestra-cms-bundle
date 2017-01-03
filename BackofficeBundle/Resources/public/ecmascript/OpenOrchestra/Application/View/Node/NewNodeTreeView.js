import OrchestraView    from '../OrchestraView'
import Nodes            from '../../Collection/Node/Nodes'
import ApplicationError from '../../../Service/Error/ApplicationError'
import Application      from '../../Application'

/**
 * @class NewNodeTreeView
 */
class NewNodeTreeView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
    }

    /**
     * Initialize
     * @param {NodeTree} nodesTree
     * @param {string}   language
     * @param {string}   parentId
     */
    initialize({nodesTree, language, parentId}) {
        this._nodesTree = nodesTree;
        this._parentId = parentId;
        this._language = language;
    }

    /**
     * Render node tree
     */
    render() {
        let template = this._renderTemplate('Node/newNodeTreeView',
            {
                nodesTree : this._nodesTree.models,
                language: this._language,
                parentId: this._parentId,
                siteLanguages: Application.getContext().siteLanguages
            }
        );

        this.$el.html(template);

        return this;
    }
}

export default NewNodeTreeView;
