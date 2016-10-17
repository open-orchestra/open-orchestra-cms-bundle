import LoaderView from '../View/Loader/LoaderView'

/**
 * @class OrchestraController
 */
class OrchestraController {

    /**
     * @param {Object} $region - Jquery selector
     * @private
     */
    _diplayLoader($region) {
        let loaderView = new LoaderView();
        $region.html(loaderView.$el);
    }
}

export default OrchestraController;
