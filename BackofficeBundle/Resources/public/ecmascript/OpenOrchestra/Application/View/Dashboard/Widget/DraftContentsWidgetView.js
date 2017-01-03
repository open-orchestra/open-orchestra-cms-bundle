import OrchestraView      from '../../OrchestraView'
import AbstractWidgetView from '../AbstractWidgetView'

/**
 * @class DraftContentsWidgetView
 */
class DraftContentsWidgetView extends AbstractWidgetView
{
    /**
     * Get title widget translation key
     *
     * @return {String}
     */
    getTitleKey() {
        return 'open_orchestra_backoffice.dashboard.content_draft_title';
    }

    /**
     * Get link edit element
     * @param {Model} entity
     *
     * @return {String}
     */
    getEditLink(entity) {
        return '';
    }
}

export default DraftContentsWidgetView;
