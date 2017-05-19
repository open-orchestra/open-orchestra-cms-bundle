import AbstractFormView from '../../../../Form/View/AbstractFormView'

/**
 * InternalLinkFormView
 */
class InternalLinkFormView extends AbstractFormView
{
    /**
     * Initialize
     *
     * @param {Form}      form
     * @param {Editor}    editor
     * @param {ModalView} modal
     */
    initialize({form, editor, modal}) {
        super.initialize({form});
        this._editor = editor;
        this._modal = modal;
    }

    /**
     * render internal link form
     */
    render() {
        this._$formRegion = this.$el;
        super.render();

        return this;
    }

    /**
     * Submit form
     * @param {object} event
     */
    _submit(event) {
        event.preventDefault();
        if (this._validForm()) {
            let serializeFields = $('form', this.$el).serializeArray();
            let oo_internal_link = {};
            for (let field of serializeFields) {
                let name = field.name.replace(/\[(.*?)\]/g, "['$1']");
                let parent = name.replace(/(.*)\[.*?\]/, "$1");
                let value = field.value.replace("'", "\\'");
                eval("if (typeof(" + parent + ") == 'undefined') {" + parent + " = {};};" + name + "= '" + value + "';");
            }
            let label = oo_internal_link['label'];
            delete(oo_internal_link['label']);
            delete(oo_internal_link['_token']);

            let $selection = $(this._editor.selection.getNode());
            if (typeof $selection.attr('data-options') !== 'undefined' && $selection.is('a')) {
                $selection.attr('data-options', JSON.stringify(oo_internal_link));
                $selection.text(label);
            } else {
                let link = tinymce.activeEditor.dom.create('a', {href: '#', 'data-options': JSON.stringify(oo_internal_link)}, tinymce.activeEditor.dom.encode(label));
                this._editor.selection.setNode(link);
            }

            Backbone.Events.trigger('form:deactivate', this);
            this._modal.hide();
        }
    }

    /**
     * @returns {boolean}
     * @private
     */
    _validForm() {
        let inputText = $('#oo_internal_link_label', this._$form);
        $('.has-error ul.help-block').remove();
        $(inputText).closest('.form-group').removeClass('has-error');

        if ('' == inputText.val()) {
            $(inputText).closest('.form-group').addClass('has-error');
            let $ul = $("<ul></ul>");
            $ul.addClass('error help-block').html("<li>"+Translator.trans('open_orchestra_backoffice.form.valid.not_blank')+"</li>");
            $(inputText).after($ul);
            $(inputText).focus();

            return false;
        }

        return true;
    }
}

export default InternalLinkFormView
