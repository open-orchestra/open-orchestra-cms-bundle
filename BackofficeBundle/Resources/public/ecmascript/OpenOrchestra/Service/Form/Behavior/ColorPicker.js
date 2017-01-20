import AbstractBehavior from './AbstractBehavior'

/**
 * @class ColorPicker
 */
class ColorPicker extends AbstractBehavior
{
    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        $element.minicolors({
            theme : 'bootstrap'
        });
    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        $element.minicolors('destroy');
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'input.colorpicker';
    }
}

export default (new ColorPicker);
