import OrchestraView      from '../../OrchestraView'
import AbstractWidgetView from '../AbstractWidgetView'

/**
 * @class LastContentsWidgetView
 */
class LastContentsWidgetView extends AbstractWidgetView
{
    /**
     * Get title widget translation key
     *
     * @return {String}
     */
    getTitleKey() {
        return 'open_orchestra_backoffice.dashboard.last_content_title';
    }

    /**
     * Get link edit element
     *
     * @return {String}
     * @todo Add link when edit content is refacto
     */
    getEditLink() {
        return '';
    }
}

export default LastContentsWidgetView;
