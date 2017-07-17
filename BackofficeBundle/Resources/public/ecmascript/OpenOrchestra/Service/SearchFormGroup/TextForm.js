import TemplateManager         from 'OpenOrchestra/Service/TemplateManager'
import AbstractSearchFormGroup from 'OpenOrchestra/Service/SearchFormGroup/AbstractSearchFormGroup'

/**
 * @class TextForm
 */
class TextForm extends AbstractSearchFormGroup
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        return field.search == 'text';
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    render(field) {
        return TemplateManager.get('SearchFormGroup/textForm')({
            field: field
        });
    }
}

// unique instance of TextForm
export default (new TextForm);
