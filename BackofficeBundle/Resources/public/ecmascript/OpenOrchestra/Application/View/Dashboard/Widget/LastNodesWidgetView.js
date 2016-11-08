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
     *
     * @return {String}
     * @todo Add link when edit node is refacto
     */
    getEditLink() {
        return '';
    }
}

export default LastNodesWidgetView;
