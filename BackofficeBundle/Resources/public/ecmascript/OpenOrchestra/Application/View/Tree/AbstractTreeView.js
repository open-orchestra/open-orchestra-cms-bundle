import OrchestraView    from 'OpenOrchestra/Application/View/OrchestraView'
/**
 * @class AbstractTreeView
 */
class AbstractTreeView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.events = {
            'click .tree .toggle-tree'       : '_toggleChildrenTree',
            'click .tree .actions .btn-close': '_openTree',
            'click .tree .actions .btn-open' : '_closeTree'
        }
    }

    /**
     * Render tree
     */
    render() {
        this.$el.html(this._getTreeTemplate());
        this._enableTreeSortable($('.tree .children', this.$el));

        return this;
    }

    /**
     * Get the tree template
     * @return {Object}
     * @private
     */
    _getTreeTemplate() {
        throw new TypeError("Please implement abstract method _getTreeTemplate.");
    }

    /**
     * @param {Object} $tree - Jquery selector
     * @private
     */
    _enableTreeSortable($tree) {
        $tree.sortable({
            tolerance: "pointer",
            placeholder: 'ui-state-highlight',
            connectWith: '.tree .children.sortable-container',
            handle     : '.sortable-handler',
            items      : '> li.sortable-node',
            zIndex     : 20,
            start      : (event, ui) => {
                this._startDrag(event, ui);
            },
            stop       : (event, ui) => {
                this._sortAction(event, ui);
            }
        });
    }

    /**
     * @param {Object} event
     * @param {Object} ui
     * @private
     */
    _startDrag(event, ui) {
    }

    /**
     * @param {Object} event
     * @param {Object} ui
     * @private
     */
    _sortAction(event, ui) {
        throw new TypeError("Please implement abstract method _sortAction.");
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
}

export default AbstractTreeView;
