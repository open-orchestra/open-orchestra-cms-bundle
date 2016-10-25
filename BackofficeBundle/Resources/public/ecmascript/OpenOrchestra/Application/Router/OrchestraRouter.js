import LoaderView from '../View/Loader/LoaderView'

/**
 * @class OrchestraRouter
 */
class OrchestraRouter extends Backbone.Router
{
    /**
     * @param {Object} $region - Jquery selector
     * @private
     */
    _diplayLoader($region) {
        let loaderView = new LoaderView();
        $region.html(loaderView.$el);
    }
}

export default OrchestraRouter;
