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
     * Submit form with patch method to refresh form
     *
     * @param event
     * @private
     */
    _submitPatch(event) {
        let $formToPatch = $(event.target).parents('.form-to-patch').eq(0);
        let $subform = $('.subform-to-refresh', $formToPatch);
        let $form = $('form', this.$el);
        let index = $('.subform-to-refresh', $form).index($subform);

        Backbone.Events.trigger('form:deactivate', this);
        this._displayLoader($subform);

        $form.ajaxSubmit({
            type: 'PATCH',
            context: this,
            success: function(response) {
                $subform.html($('.subform-to-refresh', response).eq(index).html());
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
