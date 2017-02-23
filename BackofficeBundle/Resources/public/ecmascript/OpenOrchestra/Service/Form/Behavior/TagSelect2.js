import AbstractBehavior from './AbstractBehavior'

/**
 * @class TagSelect2
 */
class TagSelect2 extends AbstractBehavior
{
    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
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
                return term.text;
            }
        });
    }

    /**
     * deactivate behavior
     *
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        $element.select2("destroy");
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
