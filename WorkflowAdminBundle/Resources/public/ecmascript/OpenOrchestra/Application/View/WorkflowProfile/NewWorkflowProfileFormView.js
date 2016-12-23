import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import ApplicationError from '../../../Service/Error/ApplicationError'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewWorkflowProfileFormView
 */
class NewWorkflowProfileFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.submit-continue-form'] = '_submit';
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('WorkflowProfile/newWorkflowProfileFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * @param  {Object} event
     *
     * @return {Object}
     */
    getStatusCodeForm(event) {
        let statusCodeForm = {
            '422': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectEditWorkflowProfile, this)
        };

        if ($(event.currentTarget).hasClass('submit-continue-form')) {
            statusCodeForm['201'] = $.proxy(this._redirectNewWorkflowProfile, this);
        }

        return statusCodeForm;
    }

    /**
     * Redirect to edit workflow profile view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditWorkflowProfile(data, textStatus, jqXHR) {
        let workflowProfileId = jqXHR.getResponseHeader('workflowProfileId');
        if (null === workflowProfileId) {
            throw new ApplicationError('Invalid workflowProfileId');
        }
        let url = Backbone.history.generateUrl('editWorkflowProfile', {workflowProfileId: workflowProfileId});
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);

    }

    /**
     * Redirect to new workflow profile view
     *
     * @private
     */
    _redirectNewWorkflowProfile() {
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.loadUrl(Backbone.history.fragment);
    }
}

export default NewWorkflowProfileFormView;
