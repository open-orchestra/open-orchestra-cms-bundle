import TemplateManager from '../TemplateManager'

/**
 * @class TextSearchFormGroup
 */
class TextSearchFormGroup
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
        return TemplateManager.get('Content/textSearchFormGroup')({
            field: field
        });
    }
}

// unique instance of TextSearchFormGroup
export default (new TextSearchFormGroup);
