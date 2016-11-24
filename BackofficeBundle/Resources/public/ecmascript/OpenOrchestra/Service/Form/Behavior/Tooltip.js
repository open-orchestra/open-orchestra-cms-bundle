/**
 * @class Tooltip
 */
class Tooltip
{
    /**
     * activate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    activate($elements) {
        $elements.tooltip();
    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $elements - jQuery elements matching selector
     */
    deactivate($elements) {
        $elements.tooltip('destroy');
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return '[data-toggle="tooltip"]';
    }
}

export default (new Tooltip);
