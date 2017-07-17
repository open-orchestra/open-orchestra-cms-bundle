import OrchestraView           from 'OpenOrchestra/Application/View/OrchestraView'
import NodesWidget             from 'OpenOrchestra/Application/Collection/Node/NodesWidget'
import ContentsWidget          from 'OpenOrchestra/Application/Collection/Content/ContentsWidget'
import LastNodesWidgetView     from 'OpenOrchestra/Application/View/Dashboard/Widget/LastNodesWidgetView'
import DraftNodesWidgetView    from 'OpenOrchestra/Application/View/Dashboard/Widget/DraftNodesWidgetView'
import LastContentsWidgetView  from 'OpenOrchestra/Application/View/Dashboard/Widget/LastContentsWidgetView'
import DraftContentsWidgetView from 'OpenOrchestra/Application/View/Dashboard/Widget/DraftContentsWidgetView'

/**
 * @class DashboardView
 */
class DashboardView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
    }

    /**
     * Render node tree
     */
    render() {
        this._renderLastNodesWidget();
        this._renderDraftNodesWidget();
        this._renderLastContentsWidget();
        this._renderDraftContentsWidget();

        return this;
    }

    /**
     * Render last nodes widget
     * @private
     */
    _renderLastNodesWidget() {
        let lastNodesWidget = new NodesWidget();
        let lastNodesWidgetView = new LastNodesWidgetView({collection : lastNodesWidget});
        this.$el.append(lastNodesWidgetView.$el);

        lastNodesWidget.fetch({
            parameter: {published: true},
            success: () => {
                lastNodesWidgetView.render()
            }
        });
    }

    /**
     * Render draft nodes widget
     * @private
     */
    _renderDraftNodesWidget() {
        let draftNodesWidget = new NodesWidget();
        let draftNodesWidgetView = new DraftNodesWidgetView({collection : draftNodesWidget});
        this.$el.append(draftNodesWidgetView.$el);

        draftNodesWidget.fetch({
            parameter: {published: false},
            success: () => {
                draftNodesWidgetView.render()
            }
        });
    }

    /**
     * Render last contents widget
     * @private
     */
    _renderLastContentsWidget() {
        let lastContentsWidget = new ContentsWidget();
        let lastContentsWidgetView = new LastContentsWidgetView({collection : lastContentsWidget});
        this.$el.append(lastContentsWidgetView.$el);

        lastContentsWidget.fetch({
            parameter: {published: true},
            success: () => {
                lastContentsWidgetView.render()
            }
        });
    }

    /**
     * Render last draft content widget
     * @private
     */
    _renderDraftContentsWidget() {
        let draftContentsWidget = new ContentsWidget();
        let draftContentsWidgetView = new DraftContentsWidgetView({collection : draftContentsWidget});
        this.$el.append(draftContentsWidgetView.$el);

        draftContentsWidget.fetch({
            parameter: {published: false},
            success: () => {
                draftContentsWidgetView.render()
            }
        });
    }
}

export default DashboardView;
