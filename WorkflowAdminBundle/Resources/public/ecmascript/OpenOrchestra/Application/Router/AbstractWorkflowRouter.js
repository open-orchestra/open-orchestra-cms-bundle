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
                    label: Translator.trans('open_orchestra_workflow_admin.status.title'),
                    link: '#'+Backbone.history.generateUrl('listStatus'),
                    id: 'course-status'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.parameters.title'),
                    link: '#'+Backbone.history.generateUrl('editParameters'),
                    id: 'course-parameter'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.workflow_profile.title'),
                    link: '#'+Backbone.history.generateUrl('listWorkflowProfile'),
                    id: 'course-profile'
                },
                {
                    label: Translator.trans('open_orchestra_workflow_admin.transition.title'),
                    link: '#'+Backbone.history.generateUrl('editTransitions'),
                    id: 'course-transition'
                }
            ]
        ]
    }

}

export default AbstractWorkflowRouter;
