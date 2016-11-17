import TemplateManager from '../../Service/TemplateManager'

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
        parameters = _.extend({renderTemplate: this._renderTemplate}, parameters);
        return TemplateManager.get(templateName)(parameters);
    }
}

export default OrchestraView;
