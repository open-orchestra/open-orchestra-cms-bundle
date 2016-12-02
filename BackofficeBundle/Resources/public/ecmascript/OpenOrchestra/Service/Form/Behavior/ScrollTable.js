import AbstractBehavior from './AbstractBehavior'

/**
 * @class ScrollTable
 */
class ScrollTable extends AbstractBehavior
{
    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let $fixedColumn = $element.clone().addClass('clone');
        $fixedColumn.find('th:not(:first-child),td:not(:first-child)').remove();
        $fixedColumn.insertBefore($element);
        $element.wrap($('<div>').addClass('wraper'));
        $fixedColumn.find('tr').each(function (i, elem) {
            $(this).height($element.find('tr:eq(' + i + ')').height());
        });
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'table.scrollable';
    }
}

// unique instance of ScrollTable
export default (new ScrollTable);
