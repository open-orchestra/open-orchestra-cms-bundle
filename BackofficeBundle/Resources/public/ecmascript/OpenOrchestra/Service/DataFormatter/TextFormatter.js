import AbstractDataFormatter from './AbstractDataFormatter'

/**
 * @class TextFormatter
 */
class TextFormatter extends AbstractDataFormatter
{
    /**
     * return supported type
     *
     * @return string
     */
    getType() {
        return 'text';
    }

    /**
     * render the field
     *
     * @param  {string} value
     * @return string
     */
    format(value) {
        if (value.length > 20) {
            value = value.substr(0, 17) + '...';
        }

        return value;
    }
}

// unique instance of TextFormatter
export default (new TextFormatter);
