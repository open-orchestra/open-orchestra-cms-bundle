import OrchestraView from '../OrchestraView'
import BlockView     from '../Block/BlockView'

/**
 * @class AreaView
 */
class AreaView extends OrchestraView
{
    /**
     * Initialize
     * @param {Area} area
     * @param {Node} node
     */
    initialize({area, node}) {
        this._node = node;
        this._area = area;
    }

    /**
     * Render area
     */
    render() {
        let templateAddBlockLink = this._renderTemplate('Block/addBlockLink', {
            node: this._node,
            areaName: this._area.get('name'),
            position: '0'
        });
        this.$el.append(templateAddBlockLink);

        this._renderBlocks(this.$el);
        this.$el.data('area', this._area);

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderBlocks($selector) {
        if (typeof this._area.get('blocks') !== 'undefined') {
            for (let block of this._area.get('blocks').models) {
                let blockView = new BlockView({block: block, area: this._area, node: this._node});
                $selector.append(blockView.render().$el);
            }
        }
    }
}

export default AreaView;
