import AbstractDataFormatter from './AbstractDataFormatter'

/**
 * @class CsvFormatter
 */
class CsvFormatter extends AbstractDataFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'csv';
    }

    /**
     * format the value
     *
     * @param  {string} value
     * @return string
     */
    format(value) {
        return '<div class="data-list-csv">' + value.split(',').join('</div><div class="data-list-csv">') + '</div>';
    }
}

// unique instance of CsvFormatter
export default (new CsvFormatter);
