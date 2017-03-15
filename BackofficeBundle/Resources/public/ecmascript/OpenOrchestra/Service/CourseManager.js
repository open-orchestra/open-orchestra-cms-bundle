/**
 * @class CourseManager
 */
class CourseManager
{
    /**
     * initialize
     * @param {Object} navigationView
     * @param {Object} breadcrumbView
     */
    initialize(navigationView, breadcrumbView) {
        this._navigationView = navigationView;
        this._breadcrumbView = breadcrumbView;
    }

    /**
     * @param {Array} items
     */
    updateBreadcrumb(items) {
        this._breadcrumbView.setItems(items);
        this._breadcrumbView.render();
    }

    /**
     * @param {Array} items
     */
    highlightBreadcrumb(item) {
        this._breadcrumbView.highlight(item);
    }

    /**
     * @param {Array} items
     */
    highlightNavigation(item) {
        this._navigationView.highlight(item);
    }
}

// unique instance of CourseManager
export default (new CourseManager)
