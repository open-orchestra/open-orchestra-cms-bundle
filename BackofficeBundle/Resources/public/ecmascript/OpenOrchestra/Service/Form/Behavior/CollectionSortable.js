import AbstractBehavior   from './AbstractBehavior'
import Application        from '../../../Application/Application'
import SitesAvailable     from '../../../Application/Collection/Site/SitesAvailable'
import GroupListModalView from '../../../Application/View/Group/GroupListModalView'

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
        $($element).children().sortable();
    }
}

// unique instance of CollectionSortable
export default (new CollectionSortable);
