import ApplicationError from './Error/ApplicationError'

/**
 * @class TemplateManager
 */
class TemplateManager
{

    /**
     * get template
     *
     * @param {string} name
     */
    get(name) {
        let template = 'template/' + name +'._tpl.html';
        if (false === Orchestra.Template.hasOwnProperty(template)) {
            throw new ApplicationError('Template' + name + 'not found');
        }

        return Orchestra.Template['template/' + name +'._tpl.html'];
    }
}

export default (new TemplateManager)
