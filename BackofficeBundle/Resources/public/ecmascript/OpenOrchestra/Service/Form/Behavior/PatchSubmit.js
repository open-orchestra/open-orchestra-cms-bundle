import AbstractBehavior from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'

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
     * Submit form with patch method to refresh form
     *
     * @param {Object} event
     * @param {Object} context
     * @private
     */
    _submitPatch(event, context) {
        Backbone.Events.trigger('form:deactivate', this);

        let formView = this;
        let $form = $('form', this.$el);
        let $formToPatch = $(event.target).parents(context.getSelector()).eq(0);
        let $subforms = $('.subform-to-refresh', $formToPatch);
        let containers = {};
        let data;

        $subforms.each(function(index, subform){
            let $subform = $(subform);
            let $container = $subform.parent();
            if ($subform.attr('id') !== undefined) {
                formView._displayLoader($container);
                containers[$subform.attr('id')] = $container;
            }
        });

        data = $form.serializeArray();

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
