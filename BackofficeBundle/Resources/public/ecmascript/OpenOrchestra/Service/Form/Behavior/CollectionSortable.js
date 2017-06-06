import AbstractBehavior   from './AbstractBehavior'

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
        $element.sortable();
    }
}

// unique instance of CollectionSortable
export default (new CollectionSortable);
