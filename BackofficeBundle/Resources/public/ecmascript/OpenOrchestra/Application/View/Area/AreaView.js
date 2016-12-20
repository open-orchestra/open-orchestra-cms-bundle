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
     */
    initialize({area}) {
        this._area = area;
    }

    /**
     * Render area
     */
    render() {
        let templateAddBlockLink = this._renderTemplate('Block/addBlockLink');
        this.$el.append(templateAddBlockLink);

        this._renderBlocks(this.$el);

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderBlocks($selector) {
        if (typeof this._area.get('blocks') !== 'undefined') {
            for (let block of this._area.get('blocks').models) {
                let blockView = new BlockView({block: block});
                $selector.append(blockView.render().$el);
            }
        }
    }
}

export default AreaView;
