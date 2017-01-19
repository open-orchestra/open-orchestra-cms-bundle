import TemplateManager from '../TemplateManager'

/**
 * @class DateSearchFormGroup
 */
class DateSearchFormGroup
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
        return TemplateManager.get('Content/dateSearchFormGroup')({
            field: field
        });
    }
}

// unique instance of DateSearchFormGroup
export default (new DateSearchFormGroup);
