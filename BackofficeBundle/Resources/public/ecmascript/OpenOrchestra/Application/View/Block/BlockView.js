import OrchestraView    from 'OpenOrchestra/Application/View/OrchestraView'
import Application      from 'OpenOrchestra/Application/Application'
import ConfirmModalView from 'OpenOrchestra/Service/ConfirmModal/View/ConfirmModalView'
import ApplicationError from 'OpenOrchestra/Service/Error/ApplicationError'

/**
 * @class BlockView
 */
class BlockView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.className = 'block-item';
        this.events = {
            'click .delete-block': '_confirmDeleteBlock',
            'click .edit-block': '_editBlock',
            'click .read-block': '_readBlock'
        }
    }

    /**
     * Initialize
     * @param {Block} block
     * @param {Node}  node
     * @param {Area}  area
     */
    initialize({block, node, area}) {
        this._node = node;
        this._block = block;
        this._area = area;
    }

    /**
     * Render block
     */
    render() {
        let position = this._area.get('blocks').indexOf(this._block) + 1;
        let template = this._renderTemplate('Block/blockView',
            {
                block: this._block,
                node: this._node,
                area: this._area,
                position: position
            }
        );
        this.$el.html(template);
        this.$el.data('block', this._block);
        this.$el.data('area', this._area);

        return this;
    }

    /**
     * show form edit block
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _editBlock(event) {
        return this._actionBlock('editBlock', event);
    }

    /**
     * show disabled form edit block
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _readBlock(event) {
        return this._actionBlock('readBlock', event);
    }

    /**
     * action required for block
     *
     * @param {String} route
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _actionBlock(route, event){
        event.stopPropagation();
        if (true === this._block.get('transverse')) {
            throw new ApplicationError('Block transverse is not editable in this context');
        }
        let url = Backbone.history.generateUrl(route, {
            blockId: this._block.get('id'),
            nodeId: this._node.get('node_id'),
            nodeLanguage: this._node.get('language'),
            nodeVersion: this._node.get('version')
        });
        Backbone.history.navigate(url, true);

        return false;
    }

    /**
     * Show modal confirm to delete a block
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmDeleteBlock(event) {
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.block.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.block.confirm_remove.message'),
            yesCallback: this._deleteBlock,
            context: this
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * Delete block
     * @private
     */
    _deleteBlock() {
        this._block.destroy({
            apiContext: 'node',
            urlParameter: {
                nodeId: this._node.get('node_id'),
                siteId: this._node.get('site_id'),
                version: this._node.get('version'),
                language: this._node.get('language'),
                areaName: this._area.get('name')
            },
            success: () => {
                this.remove();
            }
        });

        return false;
    }
}

export default BlockView;
