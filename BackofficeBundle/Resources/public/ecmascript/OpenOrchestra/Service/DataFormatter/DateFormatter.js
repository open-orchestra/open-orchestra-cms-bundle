import AbstractDataFormatter  from './AbstractDataFormatter'
import Application            from '../../Application/Application'

/**
 * @class DateFormatter
 */
class DateFormatter extends AbstractDataFormatter
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
     * @param  {string} value
     * @return string
     */
    format(value) {
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

        let destinationDateFormat = $.datepicker.regional[Application.getContext().get('language')].dateFormat + ' H:i:s';
        let originDateFormat = Application.getConfiguration().getParameter('dateFormat');

        let dd = getGeneralPattern(originDateFormat.replace('d', '([0-9]{2})'));
        let mm = getGeneralPattern(originDateFormat.replace('m', '([0-9]{2})'));
        let yy = getGeneralPattern(originDateFormat.replace('Y', '([0-9]{4})'));
        let H = getGeneralPattern(originDateFormat.replace('H', '([0-9]{2})'));
        let i = getGeneralPattern(originDateFormat.replace('i', '([0-9]{2})'));
        let s = getGeneralPattern(originDateFormat.replace('s', '([0-9]{2})'));

        return destinationDateFormat
            .replace('dd', dd.exec(value)[1])
            .replace('mm', mm.exec(value)[1])
            .replace('yy', yy.exec(value)[1])
            .replace('H', H.exec(value)[1])
            .replace('i', i.exec(value)[1])
            .replace('s', s.exec(value)[1]);
    }
}

// unique instance of DateFormatter
export default (new DateFormatter);
