import AbstractBehavior from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

/**
 * @class Tooltip
 */
class Tooltip extends AbstractBehavior
{
    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        $element.tooltip();
    }

    /**
     * deactivate behavior
     *
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        $element.tooltip('destroy');
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
