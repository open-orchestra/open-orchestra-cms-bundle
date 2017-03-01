import AbstractTreeView from '../Tree/AbstractTreeView'
import Nodes            from '../../Collection/Node/Nodes'
import ApplicationError from '../../../Service/Error/ApplicationError'
import Application      from '../../Application'

/**
 * @class NodesTreeView
 */
class NodesTreeView extends AbstractTreeView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click .legend-panel .panel-heading'] = '_toggleLegend';
    }

    /**
     * Initialize
     * @param {Statuses} statuses
     * @param {NodeTree} nodesTree
     * @param {string}   language
     */
    initialize({statuses, nodesTree, language}) {
        this._statuses = statuses;
        this._nodesTree = nodesTree;
        this._language = language
    }

    /**
     * Get the tree template
     * @return {Object}
     * @private
     */
    _getTreeTemplate() {
        return this._renderTemplate('Node/nodesTreeView',
            {
                nodesTree : this._nodesTree.models,
                statuses: this._statuses.models,
                language: this._language,
                siteLanguages: Application.getContext().siteLanguages
            }
        );
    }

    /**
     * @param {Object} event
     * @param {Object} ui
     * @private
     */
    _sortAction(event, ui) {
        let $nodes = $(ui.item).parent().children();
        let parentId = $(ui.item).parent().parent('li').data('node-id');
        if (typeof parentId === 'undefined') {
            throw new ApplicationError('undefined parent node id');
        }
        let nodes = [];

        $.each($nodes, function(index, node) {
            nodes.push({'node_id': $(node).data('node-id')})
        });

        nodes = new Nodes(nodes);
        nodes.save({
            urlParameter: {
                'nodeId': parentId
            }
        });
    }

    /**
     * Toggle legend
     *
     * @returns {boolean}
     * @private
     */
    _toggleLegend() {
        $('.legend-panel .panel-body', this.$el).slideToggle();

        return false;
    }
}

export default NodesTreeView;
