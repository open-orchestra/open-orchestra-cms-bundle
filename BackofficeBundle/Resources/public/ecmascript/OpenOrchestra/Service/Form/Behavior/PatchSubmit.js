import AbstractBehavior from './AbstractBehavior'

/**
 * @class PatchSubmit
 */
class PatchSubmit extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     *
     * */
    getExtraEvents() {
        return {
            'change .patch-submit-change': '_submitPatch',
            'click .patch-submit-click' : '_submitPatch'
        }
    }

    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let $subforms = $('.subform-to-refresh', $element);
        let containers = {};
        $subforms.each(function(index, subform){
            let $subform = $(subform);
            let $container = $subform.parent();
            if ($subform.attr('id') !== undefined) {
                containers[$subform.attr('id')] = $container;
            }            
        });
        $element.data('subformToRefresh', containers);
    }

    /**
     * Submit form with patch method to refresh form
     *
     * @param event
     * @private
     */
    _submitPatch(event) {
        Backbone.Events.trigger('form:deactivate', this);

        let context = this;
        let $form = $('form', this.$el);
        let $formToPatch = $(event.target).parents('.form-to-patch').eq(0);
        let containers = $formToPatch.data('subformToRefresh');
        let data = $form.serializeArray();

        $.each(containers, function(id, $container){
            context._displayLoader($container);
        });
        $form.ajaxSubmit({
            type: 'PATCH',
            context: this,
            data: data,
            success: function(response) {
                $.each(containers, function(id, $container){
                    let $subform = $('#' + id, response);
                    $container.html(($subform.length > 0) ? $subform.parent().html() : '');
                });
                Backbone.Events.trigger('form:activate', this);
            }
        });
    }
    
    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.form-to-patch';
    }
}

export default (new PatchSubmit);
