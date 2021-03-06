import AbstractCollectionView  from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import WorkflowProfileListView from 'OpenOrchestra/Application/View/WorkflowProfile/WorkflowProfileListView'
import GraphicView             from 'OpenOrchestra/Application/View/Transition/GraphicView'
import Application             from 'OpenOrchestra/Application/Application'

/**
 * @class WorkflowProfilesView
 */
class WorkflowProfilesView extends AbstractCollectionView
{
    /**
     * Render workflow profiles view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_workflow_admin.workflow_profile.title_list'),
                urlAdd: '#'+Backbone.history.generateUrl('newWorkflowProfile')
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('WorkflowProfile/workflowProfileView',
            {
                language: Application.getContext().get('language')
            });
            this.$el.html(template);
            this._listView = new WorkflowProfileListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.workflow-profile-list', this.$el).html(this._listView.render().$el);
            $('.graphic-workflow-preview', this.$el).html(new GraphicView().render().$el);
        }



        return this;
    }
}

export default WorkflowProfilesView;
