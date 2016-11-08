import OrchestraView      from '../../OrchestraView'
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
     *
     * @return {String}
     * @todo Add link when edit node is refacto
     */
    getEditLink() {
        return '';
    }
}

export default DraftNodesWidgetView;
