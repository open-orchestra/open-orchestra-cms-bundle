import AbstractBehavior from './AbstractBehavior'

/**
 * @class ContentType
 */
class ContentType extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'change input#oo_content_type_definingStatusable': $.proxy(this._toggleStatusable, this)
        }
    }
    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        if (!$('input#oo_content_type_definingStatusable', $element).first().prop('checked')) {
            let checkbox = $('input#oo_content_type_defaultListable_status', $element).first();
            checkbox.prop('checked', false);
            checkbox.closest('.default-listable-option').css('visibility', 'hidden');
        };
    }

    /**
     * toggle statusable behavior of the form
     * 
     * @param event
     * @private
     */
    _toggleStatusable(event) {
        if (event.currentTarget.checked) {
            this._enableWorkflow();
        } else {
            this._disableWorkflow();
        }
    }

    /**
     * Enable default listable Workflow option
     * 
     * @private
     */
    _enableWorkflow() {
        $('input#oo_content_type_defaultListable_status').closest('.default-listable-option').css('visibility', 'visible');
    }

    /**
     * Disable default listable Workflow option
     * 
     * @private
     */
    _disableWorkflow() {
        $('input#oo_content_type_defaultListable_status').prop('checked', false);
        $('input#oo_content_type_defaultListable_status').closest('.default-listable-option').css('visibility', 'hidden');
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'form[name="oo_content_type"]';
    }
}

export default (new ContentType);
