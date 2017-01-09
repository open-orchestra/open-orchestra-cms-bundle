import AbstractCollectionView  from '../../../Service/DataTable/View/AbstractCollectionView'
import WorkflowProfileListView from '../../View/WorkflowProfile/WorkflowProfileListView'
import Application             from '../../Application'

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
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('WorkflowProfile/workflowProfileView',
            {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new WorkflowProfileListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.workflow-profile-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default WorkflowProfilesView;
