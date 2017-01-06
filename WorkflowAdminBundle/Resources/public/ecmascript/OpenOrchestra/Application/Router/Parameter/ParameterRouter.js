import AbstractWorkflowRouter from '../AbstractWorkflowRouter'
import Application            from '../../Application'
import FormBuilder            from '../../../Service/Form/Model/FormBuilder'
import ParametersFormView     from '../../View/Parameter/ParametersFormView'

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
     * Edit Parameters
     */
    editParameters() {
        let url = Routing.generate('open_orchestra_workflow_admin_parameters_form');
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let parametersFormView = new ParametersFormView({form: form});
            Application.getRegion('content').html(parametersFormView.render().$el);
        });
    }
}

export default ParameterRouter;
