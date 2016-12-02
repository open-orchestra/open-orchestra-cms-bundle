import TemplateManager  from '../../TemplateManager'
import AbstractBehavior from './AbstractBehavior'

/**
 * @class NodeTemplateSelection
 */
class NodeTemplateSelection extends AbstractBehavior
{
    /**
    * get extra events
    *
    * @return {Object}
    *
    * */
    getExtraEvents() {
        return {
            'change input[name="radioNodeTemplateChoice"]': '_toggleChoice'
        }
    }

    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery elements matching selector
     */
    activate($element) {
        let template = TemplateManager.get('Form/Behavior/NodeTemplateSelection')();
        let $template = $(template);
        $('.input-node-template-selection', $template).html($element.clone());
        $element.html($template);

        $('.input-node-template-selection .form-group', $element).hide();
        let inputId = $('input[name="radioNodeTemplateChoice"]:checked', $element).val();
        $('#' + inputId, $element).closest('.form-group').show();
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _toggleChoice(event) {
        $('.input-node-template-selection .form-group', $element).hide();
        let inputId = $(event.currentTarget).val();
        $('#' + inputId, $element).closest('.form-group').show();
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return '#oo_node_nodeTemplateSelection';
    }
}

export default (new NodeTemplateSelection);
