import ModalView from '../../../Service/Modal/View/ModalView'

/**
 * @class ConfirmPublishModalView
 */
class ConfirmPublishModalView extends ModalView
{
    /**
     * Pre initialize
     *
     * @param options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click .btn-success'] = '_confirmPublish'
    }

    /**
     * Initialize
     * @param {Status}   status
     * @param {Function} callbackConfirmPublish
     */
    initialize({status, callbackConfirmPublish}) {
        this._status = status;
        this._callbackConfirmPublish = callbackConfirmPublish;
    }

    /**
     * Render modal confirm publish version
     */
    render() {
        let template = this._renderTemplate('Node/confirmPublishModalView');
        this.$el.append(template);

        return this;
    }

    /**
     * @returns {boolean}
     * @private
     */
    _confirmPublish() {
        let saveOldPublishedVersion = $('#save_old_published_version', this.$el).is(':checked');
        this._callbackConfirmPublish(this._status, saveOldPublishedVersion);

        this.hide();
    }
}

export default ConfirmPublishModalView;
