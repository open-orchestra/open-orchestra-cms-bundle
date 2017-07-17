import TemplateManager         from 'OpenOrchestra/Service/TemplateManager'
import AbstractSearchFormGroup from 'OpenOrchestra/Service/SearchFormGroup/AbstractSearchFormGroup'

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
        return TemplateManager.get('SearchFormGroup/dateForm')({
            field: field
        });
    }
}

// unique instance of DateForm
export default (new DateForm);
