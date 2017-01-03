import OrchestraRouter from './OrchestraRouter'

/**
 * @class AbstractWorkflowRouter
 */
class AbstractWorkflowRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label:Translator.trans('open_orchestra_workflow_admin.navigation.developer.title')
            },
            {
                label: Translator.trans('open_orchestra_workflow_admin.navigation.developer.workflow')
            },
            [
                {
                    label: Translator.trans('open_orchestra_workflow_admin.navigation.developer.statuses'),
                    link: '#'+Backbone.history.generateUrl('listStatus')
                },                {
                    label: Translator.trans('open_orchestra_workflow_admin.navigation.developer.workflow'),
                    link: '#'+Backbone.history.generateUrl('listWorkflowProfile')
                }
            ]
        ]
    }

}

export default AbstractWorkflowRouter;
