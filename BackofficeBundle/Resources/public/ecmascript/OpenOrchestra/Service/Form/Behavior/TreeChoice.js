import AbstractBehavior from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

/**
 * @class TreeChoice
 */
class TreeChoice extends AbstractBehavior
{
    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let regExp = new RegExp('((\u2502|\u251C|\u2514)+)', 'g');
        $('option', $element).each((index, element) => {
            let html = $(element).html();
            let depth = parseInt($(element).attr('data-depth'));
            if (depth > 0) {
                html = Array(depth).join('\u2502') + ($(element).attr('data-last') ? '\u2514' : '\u251C') + html;
            }
            $(element).html(html);
            $(element).addClass('orchestra-tree-option-choice');
        });

        $element.select2({
            allowClear: true,
            formatResult: (term) => {
                return term.text.replace(regExp, '<span class="hierarchical">$1</span>');
            },
            formatSelection: (term) => {
                return term.text.replace(regExp, '');
            }
        })
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
        return '.orchestra-tree-choice';
    }
}

export default (new TreeChoice);
