import OrchestraView from '../OrchestraView'

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
            'click .delete-block': '_deleteBlock',
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
     * Render area
     */
    render() {
        let template = this._renderTemplate('Block/blockView',
            {
                block: this._block
            }
        );
        this.$el.html(template);
        this.$el.data('block', this._block);
        this.$el.data('area', this._area);

        return this;
    }

    /**
     * @param event
     * @returns {boolean}
     * @private
     */
    _deleteBlock(event) {
        event.stopPropagation();
        this._block.destroy({
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
