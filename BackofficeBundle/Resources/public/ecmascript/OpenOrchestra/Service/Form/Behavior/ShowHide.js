import AbstractBehavior from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

/**
 * @class ShowHide
 */
class ShowHide extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     *
     * */
    getExtraEvents() {
        return {
            'change input[type="radio"]': '_toggleForms'
        }
    }

    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element, view) {
        this._toggleForm($('[type="radio"]', $element), view);
    }

    /**
     * @param {Object} event
     * @param {Object} context
     *
     * @returns {boolean}
     * @private
     */
    _toggleForms($elements, context) {
        let name = $(event.target).attr('name');
        context._toggleForm($('[name="' + name + '"]'), this);
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _toggleForm($elements, view) {
        $elements.each(function() {
            let target = $('.' + $(this).val(), view.$el);
            $(this).is(':checked') ? target.closest('.form-group').show() : target.closest('.form-group').hide();
        });
    }


    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.show-hide';
    }
}

// unique instance of ShowHide
export default (new ShowHide);
