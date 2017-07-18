import OrchestraRouter from 'OpenOrchestra/Application/Router/OrchestraRouter'

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
                label:Translator.trans('open_orchestra_workflow_admin.menu.developer.title')
            },
            {
                label: Translator.trans('open_orchestra_workflow_admin.menu.developer.workflow')
            },
            [
                {
                    label: Translator.trans('open_orchestra_workflow_admin.status.title'),
                    link: '#'+Backbone.history.generateUrl('listStatus'),
                    id: 'navigation-status'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.parameters.title'),
                    link: '#'+Backbone.history.generateUrl('editParameters'),
                    id: 'navigation-parameter'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.workflow_profile.title'),
                    link: '#'+Backbone.history.generateUrl('listWorkflowProfile'),
                    id: 'navigation-profile'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.transition.title'),
                    link: '#'+Backbone.history.generateUrl('editTransitions'),
                    id: 'navigation-transition'
                }
            ]
        ]
    }

}

export default AbstractWorkflowRouter;
