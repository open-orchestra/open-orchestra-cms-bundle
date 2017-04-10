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
        let formName = 'oo_internal_link';
        let inputText = $('#' + formName + '_label', this.$el);

        if (this._validForm()) {
            let serializeFields = $('form', this.$el).serializeArray();
            let fields = {};
            for (let field of serializeFields) {
                if ( '' !== field) {
                    let fieldName = field.name.replace(formName, '').replace(/\]\[/g, '_').replace(/(\]|\[)/g, '');
                    if (fieldName !== '_token') {
                        fields[fieldName] = $("<div/>").text(field.value).html()
                    }
                }
            }
            let link = $('<a href="#">').html(inputText.val()).attr('data-options', JSON.stringify(fields));
            let div = $('<div>').append(link);
            this._editor.insertContent(div.html());
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
