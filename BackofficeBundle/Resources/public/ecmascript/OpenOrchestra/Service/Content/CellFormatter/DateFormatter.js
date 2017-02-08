import AbstractCellFormatter from './AbstractCellFormatter'
import Application            from '../../../Application/Application'

/**
 * @class DateFormatter
 */
class DateFormatter extends AbstractCellFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'date';
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    format(field) {
        return function(td, cellData, rowData) {
            let dateFormat = $.datepicker.regional[Application.getContext().language].dateFormat;
            let dateDetails = cellData.split(' ');
            $(td).html($.datepicker.formatDate(dateDetails[0], cellData) + ' ' + dateDetails[1]);
        }
    }
}

// unique instance of DateFormatter
export default (new DateFormatter);
