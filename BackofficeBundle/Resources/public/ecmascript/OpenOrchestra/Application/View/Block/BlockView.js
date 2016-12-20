import OrchestraView from '../OrchestraView'

/**
 * @class BlockView
 */
class BlockView extends OrchestraView
{
    /**
     * Initialize
     * @param {Block} block
     */
    initialize({block}) {
        this._block = block;
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

        return this;
    }
}

export default BlockView;
