import AbstractBehavior   from './AbstractBehavior'

/**
 * @class TreeCheck
 */
class TreeCheck extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'click input[type="checkbox"]': '_changeCheckbox',
            'click .toggle-tree' : '_toggleChildrenTree'
        }
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.perimeter-tree';
    }

    /**
     * change checked status
     *
     * @param {Object} event - event object
     */
    _changeCheckbox(event) {
        let checkbox = $(event.target);
        let checked = checkbox.is(':checked');
        if (!checked) {
            checkbox.parents("ul").prev('div').find('input[type="checkbox"]').prop('checked', false);
        } else {
            checkbox.closest('li').find('input[type="checkbox"]').prop('checked', true);
        }
    }

    /**
     * @param {Object} event
     * @private
     */
    _toggleChildrenTree(event) {
        $(event.target).toggleClass('closed').parents("div").next('ul').slideToggle();
    }
}

// unique instance of TreeCheck
export default (new TreeCheck);
