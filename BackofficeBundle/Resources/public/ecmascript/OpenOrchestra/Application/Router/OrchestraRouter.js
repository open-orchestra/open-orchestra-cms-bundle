import LoaderView     from '../View/Loader/LoaderView'
import Application    from '../Application'

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
    _diplayLoader($region) {
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
    }

    /**
     * @returns {Array}
     * @private
     */
    getBreadcrumb() {
        return [];
    }

    /**
     * @param {Array} items
     * @private
     */
    _updateBreadcrumb(items) {
        Application.breadcrumbView.setItems(items);
        Application.breadcrumbView.render();
    }
}

export default OrchestraRouter;
