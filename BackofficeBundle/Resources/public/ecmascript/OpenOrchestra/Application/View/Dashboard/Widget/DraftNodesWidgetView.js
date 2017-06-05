import AbstractWidgetView from '../AbstractWidgetView'

/**
 * @class DraftNodesWidgetView
 */
class DraftNodesWidgetView extends AbstractWidgetView
{
    /**
     * Get title widget translation key
     *
     * @return {String}
     */
    getTitleKey() {
        return 'open_orchestra_backoffice.dashboard.node_draft_title';
    }

    /**
     * Get link edit element
     * @param {Model} entity
     *
     * @return {String}
     */
    getEditLink(entity) {
        return Backbone.history.generateUrl('showNode', {
            language: entity.get('language'),
            nodeId: entity.get('node_id'),
            version: entity.get('version')
        });
    }
}

export default DraftNodesWidgetView;
