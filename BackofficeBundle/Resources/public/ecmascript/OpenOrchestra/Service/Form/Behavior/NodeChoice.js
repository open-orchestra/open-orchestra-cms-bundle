/**
 * @class NodeChoice
 */
class NodeChoice
{
    /**
     * activate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    activate($elements) {
        $elements.each((index, element) => {
            let $element = $(element);
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
        return '.orchestra-node-choice';
    }
}

export default (new NodeChoice);
