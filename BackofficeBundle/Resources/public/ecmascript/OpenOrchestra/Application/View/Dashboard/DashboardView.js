import OrchestraView           from '../OrchestraView'
import NodesWidget             from '../../Collection/Node/NodesWidget'
import ContentsWidget          from '../../Collection/Content/ContentsWidget'
import LastNodesWidgetView     from '../../View/Dashboard/Widget/LastNodesWidgetView'
import DraftNodesWidgetView    from '../../View/Dashboard/Widget/DraftNodesWidgetView'
import LastContentsWidgetView  from '../../View/Dashboard/Widget/LastContentsWidgetView'
import DraftContentsWidgetView from '../../View/Dashboard/Widget/DraftContentsWidgetView'

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
