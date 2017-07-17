import OrchestraView from 'OpenOrchestra/Application/View/OrchestraView'

/**
 * @class ModalView
 */
class ModalView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
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
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        this.remove();
    }
}

export default ModalView;
