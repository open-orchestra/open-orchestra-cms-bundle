import AbstractFormView       from '../../../Service/Form/View/AbstractFormView'
import Block                  from '../../Model/Block/Block'
import Application            from '../../Application'

/**
 * @class BlockFormView
 */
class BlockFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form} form
     * @param {string} blockLabel
     * @param {string} blockId
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    initialize({form, blockLabel, blockId, nodeId, nodeLanguage, nodeVersion}) {
        super.initialize({form: form});
        this._blockLabel = blockLabel;
        this._blockId = blockId;
        this._nodeId = nodeId;
        this._nodeLanguage = nodeLanguage;
        this._nodeVersion = nodeVersion;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Block/blockEditView', {
            blockLabel : this._blockLabel,
            nodeId: this._nodeId,
            nodeLanguage: this._nodeLanguage,
            nodeVersion: this._nodeVersion
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
            '422': $.proxy(this.refreshRender, this)
        }
    }
}

export default BlockFormView;
