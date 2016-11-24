/**
 * @class DatePicker
 */
class DatePicker
{
    /**
     * Constructor
     */
    constructor() {
        this._convertFormatDay = {
            'EEEE': 'DD',
            'EE': 'D',
            'E': 'D',
            'D': 'o'
        };
        this._convertFormatMonth = {
            'MMMM': 'MM',
            'MMM': 'M',
            'MM': 'mm',
            'M': 'm'
        };
        this._convertFormatYear = {
            'yyyy': 'yy',
        }
    }

    /**
     * activate behavior
     *
     * @param {Object} $elements - jQuery elements matching selector
     */
    activate($elements) {
        if (typeof $.fn.datepicker !== 'undefined') {
            $elements.each((index, element) => {
                let $element = $(element);
                let dataDateFormat = $element.data('dateformat') || 'yyyy-mm-dd';
                dataDateFormat = this._convertFormat(this._convertFormatYear, dataDateFormat);
                dataDateFormat = this._convertFormat(this._convertFormatMonth, dataDateFormat);
                dataDateFormat = this._convertFormat(this._convertFormatDay, dataDateFormat);
                $element.datepicker({
                    dateFormat: dataDateFormat,
                    prevText: '<i class="fa fa-chevron-left"></i>',
                    nextText: '<i class="fa fa-chevron-right"></i>'
                });
            });
        }
    }

    /**
     * deactivate behavior
     *
     * @param {Object} $elements - jQuery elements matching selector
     */
    deactivate($elements) {}

    /**
     *
     * @param {Array}  formats
     * @param {string} dateFormat
     *
     * @returns {string}
     * @private
     */
    _convertFormat(formats, dateFormat) {
        for (let format of Object.keys(formats)) {
            let dateReplace = dateFormat.replace(new RegExp(format, 'g'), formats[format]);
            if (dateReplace !== dateFormat) {
                return dateReplace;
            }
        }

        return dateFormat;
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.datepicker';
    }
}

export default (new DatePicker);
