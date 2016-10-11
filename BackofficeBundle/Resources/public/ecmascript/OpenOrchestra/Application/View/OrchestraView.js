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
     * @param {function} callback
     */
    _renderTemplate(templateName, parameters, callback) {
        TemplateManager.get(templateName, (template) => {
            callback(template(parameters));
        });
    }
}

export default OrchestraView;

