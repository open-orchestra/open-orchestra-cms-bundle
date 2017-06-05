import AbstractFormView       from '../../../Service/Form/View/AbstractFormView'

/**
 * @class BlockFormView
 */
class BlockFormView extends AbstractFormView
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {string} blockId
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    initialize({form, blockId, nodeId, nodeLanguage, nodeVersion}) {
        super.initialize({form: form});
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
            blockLabel : $("input[id*='_label']", this._form.$form).first().val(),
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
