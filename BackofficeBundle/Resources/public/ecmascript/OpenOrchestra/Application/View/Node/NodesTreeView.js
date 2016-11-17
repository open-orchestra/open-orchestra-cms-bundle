import OrchestraView    from '../OrchestraView'
import Nodes            from '../../Collection/Node/Nodes'
import ApplicationError from '../../../Service/Error/ApplicationError'
import Application      from '../../Application'
import Statuses         from '../../Collection/Statuses/Statuses'

/**
 * @class NodesTreeView
 */
class NodesTreeView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.events = {
            'click .tree .toggle-tree' : '_toggleChildrenTree',
            'click .tree .actions .btn-close' : '_openTree',
            'click .tree .actions .btn-open' : '_closeTree',
            'click .legend-panel .panel-heading': '_toggleLegend'
        }
    }

    /**
     * Initialize
     * @param {NodeTree} nodesTree
     * @param {string} language
     */
    initialize({nodesTree, language}) {
        this._nodesTree = nodesTree;
        this._language = language
    }

    /**
     * Render node tree
     */
    render() {
        let template = this._renderTemplate('Node/nodesTreeView',
            {
                nodesTree : this._nodesTree.models,
                language: this._language,
                siteLanguages: Application.getContext().siteLanguages
            }
        );

        this.$el.html(template);
        this._enableTreeSortable($('.tree .children', this.$el));
        this._renderLegend($('.legend-panel .panel-body', this.$el));

        return this;
    }

    /**
     * Render statuses legend panel
     *
     * @param {Object} $region - Jquery selector
     * @private
     */
    _renderLegend($region) {
        this._diplayLoader($region);
        new Statuses().fetch({
            success: (statuses) => {
                let template = this._renderTemplate('Node/statusesLegend',{ statuses: statuses.models });
                $region.html(template);
            }
        });
    }

    /**
     * @param {Object} $tree - Jquery selector
     * @private
     */
    _enableTreeSortable($tree) {
        $tree.sortable({
            connectWith: '.tree .children',
            handle: '.sortable-handler',
            zIndex: 20,
            stop: (event, ui) => {
                let $nodes = $(ui.item).parent().children();
                let parentId = $(ui.item).parent().parent('li').data('node-id');
                if (typeof parentId === 'undefined') {
                    throw new ApplicationError('undefined parent node id');
                }
                let nodes = [];
                $.each($nodes, function(index, node) {
                    console.log($(node));
                    nodes.push({'node_id': $(node).data('node-id')})

                });

                nodes = new Nodes(nodes);
                nodes.save({
                    urlParameter: {
                        'nodeId': parentId
                    }
                });
            }
        });
    }

    /**
     * @param {Object} event
     * @private
     */
    _toggleChildrenTree(event) {
        $(event.target).toggleClass('closed').parents("div").next('ul').slideToggle();
    }

    /**
     * Open Tree
     *
     * @returns {boolean}
     * @private
     */
    _openTree() {
        $('.tree .toggle-tree', this.$el).removeClass('closed').parents("div").next('ul').slideUp();

        return false;
    }

    /**
     * Close tree
     *
     * @returns {boolean}
     * @private
     */
    _closeTree() {
        $('.tree .toggle-tree', this.$el).addClass('closed').parents("div").next('ul').slideDown();

        return false;
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
