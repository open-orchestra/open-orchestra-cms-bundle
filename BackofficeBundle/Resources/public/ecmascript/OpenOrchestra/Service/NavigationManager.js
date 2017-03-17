/**
 * @class NavigationManager
 */
class NavigationManager
{
    /**
     * initialize
     * @param {Object} menuView
     * @param {Object} breadcrumbView
     */
    initialize(menuView, breadcrumbView) {
        this._menuView = menuView;
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
    highlightMenu(item) {
        this._menuView.highlight(item);
    }
}

// unique instance of NavigationManager
export default (new NavigationManager)
