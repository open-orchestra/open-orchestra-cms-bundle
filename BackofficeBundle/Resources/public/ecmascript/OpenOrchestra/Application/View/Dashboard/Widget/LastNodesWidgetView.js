import OrchestraView      from '../../OrchestraView'
import AbstractWidgetView from '../AbstractWidgetView'

/**
 * @class LastNodesWidgetView
 */
class LastNodesWidgetView extends AbstractWidgetView
{
    /**
     * Get title widget translation key
     *
     * @return {String}
     */
    getTitleKey() {
        return 'open_orchestra_backoffice.dashboard.last_node_title';
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

export default LastNodesWidgetView;
