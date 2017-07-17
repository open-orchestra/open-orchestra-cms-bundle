import TemplateManager         from 'OpenOrchestra/Service/TemplateManager'
import AbstractSearchFormGroup from 'OpenOrchestra/Service/SearchFormGroup/AbstractSearchFormGroup'

/**
 * @class NumberForm
 */
class NumberForm extends AbstractSearchFormGroup
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        return field.search == 'number';
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    render(field) {
        return TemplateManager.get('SearchFormGroup/numberForm')({
            field: field
        });
    }
}

// unique instance of NumberForm
export default (new NumberForm);
