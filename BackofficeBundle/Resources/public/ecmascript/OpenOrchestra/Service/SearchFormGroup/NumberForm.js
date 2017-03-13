import TemplateManager         from '../TemplateManager'
import AbstractSearchFormGroup from './AbstractSearchFormGroup'

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
        return TemplateManager.get('Content/numberForm')({
            field: field
        });
    }
}

// unique instance of NumberForm
export default (new NumberForm);
