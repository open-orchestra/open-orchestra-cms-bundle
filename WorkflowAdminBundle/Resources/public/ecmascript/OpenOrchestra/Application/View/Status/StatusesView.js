import AbstractCollectionView from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import StatusListView         from 'OpenOrchestra/Application/View/Status/StatusListView'
import Application            from 'OpenOrchestra/Application/Application'
import GraphicView            from '.OpenOrchestra/Application/View/Transition/GraphicView'

/**
 * @class StatusesView
 */
class StatusesView extends AbstractCollectionView
{
    /**
     * Render statuses view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_workflow_admin.status.title_list'),
                urlAdd: '#' + Backbone.history.generateUrl('newStatus')
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('Status/statusesView',
            {
                language: Application.getContext().get('language')
            });
            this.$el.html(template);
            this._listView = new StatusListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.statuses-list', this.$el).html(this._listView.render().$el);
            $('.graphic-workflow-preview', this.$el).html(new GraphicView().render().$el);
        }

        return this;
    }
}

export default StatusesView;
