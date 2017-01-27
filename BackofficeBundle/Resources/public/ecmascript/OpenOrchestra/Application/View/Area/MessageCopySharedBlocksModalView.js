import ModalView          from '../../../Service/Modal/View/ModalView'

/**
 * @class MessageCopySharedBlocksModalView
 */
class MessageCopySharedBlocksModalView extends ModalView
{
    /**
     * Initialize
     * @param {Array}    sharedBlocks
     */
    initialize({sharedBlocks}) {
        this._sharedBlocks = sharedBlocks;
    }

    /**
     * Render modal select node copy block
     */
    render() {
        let template = this._renderTemplate('Area/messageCopySharedBlocksModalView', {
            sharedBlocks: this._sharedBlocks,
            sharedBlocksStringList : this._blocksToString()
        });
        this.$el.append(template);

        return this;
    }

    /**
     * Convert list of shared blocks to string
     *
     * @returns {string}
     * @private
     */
    _blocksToString() {
        let res = '';
        for (let block of this._sharedBlocks) {
            res = res + block.get('label')+'['+ block.get('name')+ '] ';
        }

        return res;
    }
}

export default MessageCopySharedBlocksModalView;
