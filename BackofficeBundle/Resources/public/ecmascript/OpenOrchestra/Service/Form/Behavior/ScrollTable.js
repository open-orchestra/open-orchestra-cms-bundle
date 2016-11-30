/**
 * @class ScrollTable
 */
class ScrollTable
{
    /**
     * activate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    activate($elements) {
        $elements.each(function() {
            let $table = $(this);
            let $fixedColumn = $table.clone().addClass('clone');
            $fixedColumn.find('th:not(:first-child),td:not(:first-child)').remove();
            $fixedColumn.insertBefore($table);
            $table.wrap($('<div>').addClass('wraper'));
            $fixedColumn.find('tr').each(function (i, elem) {
                $(this).height($table.find('tr:eq(' + i + ')').height());
            });
        });
    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    deactivate($elements) {
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
