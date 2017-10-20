import AbstractBehavior from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'
import AlertModalView   from 'OpenOrchestra/Service/AlertModal/View/AlertModalView'
import Application                      from 'OpenOrchestra/Application/Application'

/**
 * @class AlertValueChange
 */
class AlertValueChange extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     *
     * */
    getExtraEvents() {
        return {
            'change': '_alertValueChange'
        }
    }

    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let val = $element.val();
        let $option = $('option[value="' + val + '"]', $element);
        $element.attr('data-value', $option.data('value') || val);
    }

    /**
     * Toggle block when update video type input
     *
     * @param event
     * @private
     */
    _alertValueChange(event) {
        let $element = $(event.currentTarget);
        let val = $element.val();
        let $option = $('option[value="' + val + '"]', $element);
        val = $option.data('value') || val;
        if (val !== $element.data('value')) {
            let alertModalView = new AlertModalView({
                title: $element.data('title'),
                message: $element.data('message')
            });
            Application.getRegion('modal').html(alertModalView.render().$el);
            alertModalView.show();
        }
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.alert-value-change';
    }
}

export default (new AlertValueChange);
