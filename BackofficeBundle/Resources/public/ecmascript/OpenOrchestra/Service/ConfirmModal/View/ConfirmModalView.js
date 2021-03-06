import ModalView from 'OpenOrchestra/Service/Modal/View/ModalView'

/**
 * @class ConfirmModalView
 */
class ConfirmModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize({confirmTitle, confirmMessage, yesCallback, context, callbackParameter = [], noCallback}) {
        super.preinitialize();
        this._confirmTitle = confirmTitle;
        this._confirmMessage = confirmMessage;
        this._yesCallback = yesCallback;
        this._context = context;
        this._callbackParameter = callbackParameter;
        this.events['click .btn-success'] = '_yesConfirm';
        if (noCallback) {
            this.events['click .btn-danger'] = '_noConfirm';
            this._noCallback = noCallback;
        }
    }

    /**
     * Render confirm modal
     */
    render() {
        let template = this._renderTemplate('ConfirmModal/confirmModalView', {
            confirmTitle: this._confirmTitle,
            confirmMessage: this._confirmMessage
        });
        this.$el.html(template);

        return this;
    }

    /**
     * Success Confirm , call yes callback and remove modal
     *
     * @private
     */
    _yesConfirm() {
        this._yesCallback.apply(this._context, this._callbackParameter);
        this.hide();
    }

    /**
     * Danger Confirm , call no callback
     *
     * @private
     */
    _noConfirm() {
        this._noCallback.apply(this._context, this._callbackParameter);
    }
}

export default ConfirmModalView;
