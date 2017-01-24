/**
 * @class AbstractSearchFormGroup
 */
class AbstractSearchFormGroup
{
    /**
     * test if field is supported
     *
     * @param {Object} field
     */
    support(field) {
        throw new Error('Missing support method');
    }

    /**
     * render the field
     *
     * @param {Object} field
     */
    render(field) {
        throw new Error('Missing render method');
    }
}

export default AbstractSearchFormGroup;
