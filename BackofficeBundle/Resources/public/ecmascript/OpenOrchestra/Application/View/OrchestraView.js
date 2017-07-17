import TemplateManager from 'OpenOrchestra/Service/TemplateManager'
import LoaderView      from 'OpenOrchestra/Application/View/Loader/LoaderView'

/**
 * @class OrchestraView
 */
class OrchestraView extends Backbone.View
{
    /**
     * Get underscore template and it with parameters
     *
     * @param {String}   templateName
     * @param {object}   parameters
     */
    _renderTemplate(templateName, parameters = {}) {
        parameters = $.extend(true, {}, {renderTemplate: this._renderTemplate}, parameters);

        return TemplateManager.get(templateName)(parameters);
    }

    /**
     * @param {Object} $region - Jquery selector
     * @private
     */
    _displayLoader($region) {
        let loaderView = new LoaderView();
        $region.html(loaderView.$el);
    }
}

export default OrchestraView;
