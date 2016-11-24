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
            let $fixedColumn = $table.clone().insertBefore($table).css({
                position: 'absolute',
                display: 'inline-block',
                width: 'auto',
                borderRight: '1px dotted #000000',
                backgroudColor: '#000000',
                zIndex: 2,
            });
            $table.css({zIndex: 1}).wrap($('<div>').css({overflow: 'auto'}));
            $fixedColumn.find('th:not(:first-child),td:not(:first-child)').remove();
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
