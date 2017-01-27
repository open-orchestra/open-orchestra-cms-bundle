import AbstractFormView       from '../../../Service/Form/View/AbstractFormView'
import FlashMessageBag        from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage           from '../../../Service/FlashMessage/FlashMessage'
import Block                  from '../../Model/Block/Block'
import Nodes                  from '../../Collection/Node/Nodes'
import NodeUsageBlockListView from '../Node/NodeUsageBlockListView'
import Application            from '../../Application'
import ConfirmModalView       from '../../../Service/ConfirmModal/View/ConfirmModalView'

/**
 * @class SharedBlockFormView
 */
class SharedBlockFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.delete-button'] = '_confirmDeleteBlock';
    }

    /**
     * Initialize
     * @param {Form}    form
     * @param {string}  blockLabel
     * @param {string}  blockId
     * @param {string}  language
     * @param {boolean} activateUsageTab
     */
    initialize({form, blockLabel, blockId, language, activateUsageTab}) {
        super.initialize({form: form});
        this._blockLabel = blockLabel;
        this._blockId = blockId;
        this._language = language;
        this._activateUsageTab = activateUsageTab;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Block/sharedBlockEditView', {
            blockLabel : this._blockLabel,
            language: this._language,
            messages: FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritdoc
     */
    _renderForm() {
        super._renderForm();
        this._addTabUsageBlock();
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
            confirmTitle: Translator.trans('open_orchestra_backoffice.shared_block.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.shared_block.confirm_remove.message'),
            yesCallback: this._deleteBlock,
            context: this
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * Delete
     */
    _deleteBlock() {
        let block = new Block({'id': this._blockId});
        block.destroy({
            apiContext: 'shared-block',
            success: () => {
                let message = new FlashMessage(Translator.trans('open_orchestra_backoffice.shared_block.success_remove'), 'success');
                FlashMessageBag.addMessageFlash(message);
                let url = Backbone.history.generateUrl('listSharedBlock',{
                    language: this._language
                });
                Backbone.history.navigate(url, true);
            }
        });
    }

    /**
     * Add tab pane usage block
     * @private
     */
    _addTabUsageBlock() {
        let tabId = 'tab-usage-block';
        let $navTab = $('<li/>').append(
            $('<a/>', {
                text: Translator.trans('open_orchestra_backoffice.shared_block.tab_usage_block'),
                'data-toggle': 'tab',
                role: 'tab',
                href: '#'+tabId
        }));
        let $tabContent = $('<div/>', {
            class: 'tab-pane',
            id: tabId,
            role: 'tabpanel'
        });

        $('.nav-tabs', this._$formRegion).append($navTab);
        $('.tab-content', this._$formRegion).append($tabContent);

        let listView = this._createListNodeUsageBlockView();
        $tabContent.html(listView.render().$el);
        if (true === this._activateUsageTab) {
            $('.nav-tabs a[href="#'+tabId+'"]', this._$formRegion).tab('show');
            $('.tab-content .tab-pane', this._$formRegion).removeClass('active');
            $tabContent.addClass('active');
        }
    }

    /**
     * @returns {NodeUsageBlockListView}
     *
     * @private
     */
    _createListNodeUsageBlockView() {
        let collection = new Nodes();
        let language = this._language;
        let siteId = Application.getContext().siteId;

        return new NodeUsageBlockListView({
            collection: collection,
            language: language,
            siteId: siteId,
            blockId: this._blockId
        });
    }
}

export default SharedBlockFormView;