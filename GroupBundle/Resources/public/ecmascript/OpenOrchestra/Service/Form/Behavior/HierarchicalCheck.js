import AbstractBehavior   from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

/**
 * @class HierarchicalCheck
 */
class HierarchicalCheck extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'click input[type="checkbox"]': '_changeCheckbox'
        }
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.hierarchical-check-list tr';
    }

    /**
     * change checked status
     *
     * @param {Object} event - event object
     */
    _changeCheckbox(event) {
        let checkbox = $(event.target);
        let checkboxes = checkbox.closest('tr').find('input[type="checkbox"]');
        let checked = checkbox.is(':checked');
        if (!checked) {
            jQuery.fn.reverse = [].reverse;
            checkboxes.reverse();
        }
        checkboxes.each(function(){
            $(this).prop('checked', checked);
            if ($(this).attr('id') == checkbox.attr('id')) {
                return false;
            }
        });
    }
}

// unique instance of HierarchicalCheck
export default (new HierarchicalCheck);
