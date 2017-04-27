import AbstractDataFormatter from './AbstractDataFormatter'

/**
 * @class StatusFormatter
 */
class StatusFormatter extends AbstractDataFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'status';
    }

    /**
     * format the value
     *
     * @param  {string} value
     * @return string
     */
    format(value) {
        return "<span style='color:" + value.attributes.code_color + ";'>" + value.attributes.label + "</span>";
    }
}

export default (new StatusFormatter);
