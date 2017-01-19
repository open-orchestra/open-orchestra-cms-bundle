import TemplateManager from '../TemplateManager'

/**
 * @class NumberSearchFormGroup
 */
class NumberSearchFormGroup
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
        return TemplateManager.get('Content/numberSearchFormGroup')({
            field: field
        });
    }
}

// unique instance of NumberSearchFormGroup
export default (new NumberSearchFormGroup);
