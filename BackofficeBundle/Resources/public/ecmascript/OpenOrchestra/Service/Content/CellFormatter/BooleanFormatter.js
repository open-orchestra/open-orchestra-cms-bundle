import AbstractCellFormatter from './AbstractCellFormatter'

/**
 * @class BooleanFormatter
 */
class BooleanFormatter extends AbstractCellFormatter
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        return field.type == 'bool';
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    format(field) {
        return function(td, cellData, rowData) {
            let $icon = $('<i>', {'aria-hidden': 'true'});
            if(cellData) {
                $icon.addClass('fa fa-check text-success');
            } else {
                $icon.addClass('fa fa-close text-danger');
            } 
            
            $(td).html($icon)
        };
    }
}

// unique instance of BooleanFormatter
export default (new BooleanFormatter);
