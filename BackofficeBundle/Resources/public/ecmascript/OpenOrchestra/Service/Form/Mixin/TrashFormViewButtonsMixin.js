import ConfirmModalView     from 'OpenOrchestra/Service/ConfirmModal/View/ConfirmModalView'
import Application          from 'OpenOrchestra/Application/Application'
import FormViewButtonsMixin from 'OpenOrchestra/Service/Form/Mixin/FormViewButtonsMixin'

let TrashFormViewButtonsMixin = (superclass) => class extends mix(superclass).with(FormViewButtonsMixin) {

    /**
     * Show modal confirm to delete models
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmDelete(event) {
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove.trash'),
            yesCallback: this._deleteElement,
            context: this
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }
};

export default TrashFormViewButtonsMixin;
