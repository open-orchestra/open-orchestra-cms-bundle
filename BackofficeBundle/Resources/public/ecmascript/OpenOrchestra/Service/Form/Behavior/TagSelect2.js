/**
 * @class TagSelect2
 */
class TagSelect2
{
    /**
     * activate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    activate($elements) {
        $elements.each((index, element) => {
            let $element = $(element);
            let tags = $element.data('tags');
            $element.select2({
                tags: tags,
                containerCssClass: 'tags',
                createSearchChoice: (term, data) => {
                    if (!$element.data('authorize-new')) {
                        return false;
                    }

                    return { id: term, text: term, isNew: true };
                },
                formatResult: (term) => {
                    if (term.isNew) {
                        return "<span class='label label-danger'>" + Translator.trans('open_orchestra_backoffice.form.keyword.new') + "</span> " + term.text;
                    } else {
                        return term.text
                    }
                },
                formatSelection: (term, container) => {
                    if (term.isNew) {
                       container.parent().addClass('bg-color-red')
                    }

                    return term.text;
                }
            });
        });
    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    deactivate($elements) {
        $elements.each((index, element) => {
            $(element).select2("destroy");
        });
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return '.select2';
    }
}

export default (new TagSelect2);
