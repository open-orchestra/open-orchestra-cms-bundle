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

            let getGeneralPattern = function (originPattern) {
                return new RegExp(originPattern
                .replace('d', '[0-9]{2}')
                .replace('Y', '[0-9]{4}')
                .replace('m', '[0-9]{2}')
                .replace('H', '[0-9]{2}')
                .replace('i', '[0-9]{2}')
                .replace('s', '[0-9]{2}')
                .replace('O', '.*')
                .replace(/\:/g, '\\:'));
            };
            
            let destinationDateFormat = $.datepicker.regional[Application.getContext().language].dateFormat + ' H:i:s';
            let originDateFormat = Application.getContext().dateFormat
            
            let dd = getGeneralPattern(originDateFormat.replace('d', '([0-9]{2})'));
            let mm = getGeneralPattern(originDateFormat.replace('m', '([0-9]{2})'));
            let yy = getGeneralPattern(originDateFormat.replace('Y', '([0-9]{4})'));
            let H = getGeneralPattern(originDateFormat.replace('H', '([0-9]{2})'));
            let i = getGeneralPattern(originDateFormat.replace('i', '([0-9]{2})'));
            let s = getGeneralPattern(originDateFormat.replace('s', '([0-9]{2})'));
            
            $(td).html(destinationDateFormat
                .replace('dd', dd.exec(cellData)[1])
                .replace('mm', mm.exec(cellData)[1])
                .replace('yy', yy.exec(cellData)[1])
                .replace('H', H.exec(cellData)[1])
                .replace('i', i.exec(cellData)[1])
                .replace('s', s.exec(cellData)[1]));
        }
    }
}

// unique instance of DateFormatter
export default (new DateFormatter);
