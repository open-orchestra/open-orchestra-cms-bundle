import AbstractBehavior   from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

/**
 * @class CollectionSortable
 */
class CollectionSortable extends AbstractBehavior
{
    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.collection-sortable';
    }

    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        // special selector for bootstrap_collection formtype
        if ($('.bc-collection', $element).length > 0) {
            $element = $('.bc-collection', $element);
        }
        $element.sortable({
            axis: "y",
            handle: $('.sortable-handler', $element).length > 0 ? ".sortable-handler" : false,
        });
    }
}

// unique instance of CollectionSortable
export default (new CollectionSortable);
