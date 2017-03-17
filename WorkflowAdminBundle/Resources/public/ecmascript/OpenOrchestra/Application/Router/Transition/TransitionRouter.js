import AbstractWorkflowRouter from '../AbstractWorkflowRouter'
import Application            from '../../Application'
import TransitionsFormView    from '../../View/Transition/TransitionsFormView'
import FormBuilder            from '../../../Service/Form/Model/FormBuilder'

/**
 * @class TransitionRouter
 */
class TransitionRouter extends AbstractWorkflowRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'workflow/transitions': 'editTransitions'
        };
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-workflow'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumbHighlight() {
        return {
            '*' : 'navigation-transition'
        };
    }

    /**
     * Edit Transitions
     */
    editTransitions() {
        let url = Routing.generate('open_orchestra_workflow_admin_transitions_form');
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let parametersFormView = new TransitionsFormView({form: form});
            Application.getRegion('content').html(parametersFormView.render().$el);
        });
     }
}

export default TransitionRouter;
