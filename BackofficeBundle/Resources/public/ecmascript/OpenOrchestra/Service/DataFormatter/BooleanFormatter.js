import AbstractDataFormatter from './AbstractDataFormatter'

/**
 * @class BooleanFormatter
 */
class BooleanFormatter extends AbstractDataFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'bool';
    }

    /**
     * format the value
     *
     * @param  {string} value
     * @return string
     */
    format(value) {
        let $icon = $('<i>', {'aria-hidden': 'true'});
        if(value) {
            $icon.addClass('fa fa-check text-success');
        } else {
            $icon.addClass('fa fa-close text-danger');
        }

        return $icon;
    }
}

// unique instance of BooleanFormatter
export default (new BooleanFormatter);
