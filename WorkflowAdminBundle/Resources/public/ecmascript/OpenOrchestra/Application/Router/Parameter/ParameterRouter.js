import AbstractWorkflowRouter from 'OpenOrchestra/Application/Router/AbstractWorkflowRouter'
import Application            from 'OpenOrchestra/Application/Application'
import FormBuilder            from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import ParametersFormView     from 'OpenOrchestra/Application/View/Parameter/ParametersFormView'

/**
 * @class ParameterRouter
 */
class ParameterRouter extends AbstractWorkflowRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'workflow/parameters/edit': 'editParameters',
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
            '*' : 'navigation-parameter'
        };
    }

    /**
     * Edit Parameters
     */
    editParameters() {
        let url = Routing.generate('open_orchestra_workflow_admin_parameters_form');
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let parametersFormView = new ParametersFormView({form: form});
            Application.getRegion('content').html(parametersFormView.render().$el);
        });
    }
}

export default ParameterRouter;
