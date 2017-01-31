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
            'change .patch-submit': '_submitPatch',
            'click .patch-submit' : '_submitPatch'
        }
    }

    /**
     * Submit form with patch method to refresh form
     *
     * @param event
     * @private
     */
    _submitPatch(event) {
        let $form = $(event.target).parents('form').eq(0);

        Backbone.Events.trigger('form:deactivate', $form);
        this._displayLoader($form);

        $form.ajaxSubmit({
            method: 'PATCH',
            context: this,
            success: function(response) {
                let $newForm = $(response);
                $form.replaceWith($newForm);
                Backbone.Events.trigger('form:deactivate', $newForm);;
            }
        });
    }
    
    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return 'form';
    }
}

export default (new PatchSubmit);
