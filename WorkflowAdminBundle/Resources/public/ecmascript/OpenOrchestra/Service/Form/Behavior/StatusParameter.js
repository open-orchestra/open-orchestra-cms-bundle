import AbstractBehavior from './AbstractBehavior'

/**
 * @class StatusParameter
 */
class StatusParameter extends AbstractBehavior
{
    /**
    * get extra events
    *
    * @return {Object}
    * */
    getExtraEvents() {
        return {
            'click input[type="radio"]': '_toggleChoice'
        }
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _toggleChoice(event) {
        let chosenRadio = $(event.currentTarget);
        let radioGroupName = chosenRadio.data('group');
        let chosenRadioName = chosenRadio.attr('name');
        let radioGroup = $('input:checked[type="radio"][data-group="' + radioGroupName + '"][name!="' + chosenRadioName + '"]');
        radioGroup.each(function() {$(this).prop('checked', false);});
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'form[name="oo_workflow_parameters"]';
    }
}

export default (new StatusParameter);
