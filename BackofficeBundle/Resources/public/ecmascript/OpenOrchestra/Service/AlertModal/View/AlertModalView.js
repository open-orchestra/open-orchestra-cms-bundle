import ModalView from 'OpenOrchestra/Service/Modal/View/ModalView'

/**
 * @class AlertModalView
 */
class AlertModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize({title, message}) {
        super.preinitialize();
        this._title = title;
        this._message = message;
    }

    /**
     * Render confirm modal
     */
    render() {
        let template = this._renderTemplate('AlertModal/alertModalView', {
            title: this._title,
            message: this._message
        });
        this.$el.html(template);

        return this;
    }
}

export default AlertModalView;
