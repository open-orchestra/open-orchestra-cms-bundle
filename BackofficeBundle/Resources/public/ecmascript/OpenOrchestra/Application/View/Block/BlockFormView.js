import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import Block            from '../../Model/Block/Block'

/**
 * @class BlockFormView
 */
class BlockFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.delete-button'] = '_deleteBlock';
    }

    /**
     * Initialize
     * @param {Form} form
     * @param {string} blockLabel
     * @param {string} blockId
     */
    initialize({form, blockLabel, blockId}) {
        super.initialize({form: form});
        this._blockLabel = blockLabel;
        this._blockId = blockId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Block/blockEditView', {
            blockLabel : this._blockLabel,
            messages: FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritDoc
     */
    getStatusCodeForm(event) {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }

    /**
     * Delete
     */
    _deleteBlock() {
        let block = new Block({'id': this._blockId});
        block.destroy({
            context: 'shared-block',
            success: () => {
                let url = Backbone.history.generateUrl('listSharedBlock');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default BlockFormView;
