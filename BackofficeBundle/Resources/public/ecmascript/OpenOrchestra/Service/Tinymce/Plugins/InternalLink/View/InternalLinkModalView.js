import ModalView            from '../../../../Modal/View/ModalView'
import FormBuilder          from '../../../../Form/Model/FormBuilder'
import InternalLinkFormView from './InternalLinkFormView'

/**
 * InternalLinkModalView
 */
class InternalLinkModalView extends ModalView
{
    /**
     * Initialize
     * @param {Editor} editor
     * @param {Object} data
     */
    initialize({editor, data}) {
        super.initialize();
        this._editor = editor;
        this._data = data;
    }

    /**
     * render internal link modal form
     */
    render() {
        let template = this._renderTemplate('Tinymce/InternalLink/internalLinkForm');
        this.$el.html(template);
        let $formRegion = $('.modal-body', this.$el);
        this._displayLoader($formRegion);

        let url = Routing.generate('open_orchestra_backoffice_internal_link_form');
        FormBuilder.createFormFromUrl(url, (form) => {
            let internalLinkFormView = new InternalLinkFormView({
                form: form,
                editor: this._editor,
                modal: this
            });
            $formRegion.html(internalLinkFormView.render().$el);
        }, this._data);

        return this;
    }
}

export default InternalLinkModalView
