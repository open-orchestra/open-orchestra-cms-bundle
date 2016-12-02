import AbstractBehavior from './AbstractBehavior'

/**
 * @class NodeChoice
 */
class NodeChoice extends AbstractBehavior
{
    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let regExp = new RegExp('((\u2502|\u251C|\u2514)+)', 'g');
        $('option', $element).each((index, element) => {
            $(element).addClass('orchestra-node-choice')
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
        return '.orchestra-node-choice';
    }
}

export default (new NodeChoice);
