import TemplateManager         from '../../TemplateManager'
import AbstractSearchFormGroup from './AbstractSearchFormGroup'

/**
 * @class DateForm
 */
class DateForm extends AbstractSearchFormGroup
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        return field.search == 'date';
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    render(field) {
        return TemplateManager.get('Content/dateForm')({
            field: field
        });
    }
}

// unique instance of DateForm
export default (new DateForm);
