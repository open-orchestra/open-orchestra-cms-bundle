import OrchestraView from '../../../Application/View/OrchestraView'

/**
 * @class ModalView
 */
class ModalView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'modal fade';
        this.events = {
            'hidden.bs.modal': 'hide'
        }
    }

    /**
     * Show modal
     */
    show() {
        this.$el.modal('show');
    }

    /**
     * Hide modal
     */
    hide() {
        this.remove();
    }
}

export default ModalView;
