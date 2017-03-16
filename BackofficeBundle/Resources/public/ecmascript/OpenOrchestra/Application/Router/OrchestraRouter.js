import LoaderView    from '../View/Loader/LoaderView'
import CourseManager from '../../Service/CourseManager'

/**
 * @class OrchestraRouter
 */
class OrchestraRouter extends Backbone.Router
{
    /**
     * @inheritdoc
     */
    route(route, name, callback) {
        super.route(route, name, callback);
        Backbone.history.addRoutePattern(name, route);
    }

    /**
     * @param {Object} $region - Jquery selector
     * @private
     */
    _displayLoader($region) {
        let loaderView = new LoaderView();
        $region.html(loaderView.$el);
    }

    /**
     * @param {Function} callback
     * @param {Object}   args
     * @param {string}   name
     */
    execute(callback, args, name) {
        super.execute(callback, args, name);
        let items = this.getBreadcrumb();
        this._updateBreadcrumb(items);
        this._highlight(name);
    }

    /**
     * @returns {Array}
     * @private
     */
    getBreadcrumb() {
        return [];
    }

    /**
     * @returns {Array}
     * @private
     */
    getNavigationHighlight() {
        return {};
    }

    /**
     * @returns {Array}
     * @private
     */
    getBreadcrumbHighlight() {
        return {};
    }

    /**
     * @param {Array} items
     * @private
     */
    _updateBreadcrumb(items) {
        CourseManager.updateBreadcrumb(items);
    }

    /**
     * @param {string} name
     * @private
     */
    _highlight(name) {
        let breadcrumb = this.getBreadcrumbHighlight();
        let navigation = this.getNavigationHighlight();

        if (breadcrumb !== null && breadcrumb.hasOwnProperty(name)) {
            CourseManager.highlightBreadcrumb(breadcrumb[name]);
        }
        if (navigation !== null && navigation.hasOwnProperty(name)) {
            CourseManager.highlightNavigation(navigation[name]);
        }
    }

}

export default OrchestraRouter;
