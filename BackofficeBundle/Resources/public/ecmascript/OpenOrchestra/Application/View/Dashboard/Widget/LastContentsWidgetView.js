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
     * @param {Model} entity
     *
     * @return {String}
     */
    getEditLink(entity) {
        return Backbone.history.generateUrl('editContent', {
            language: entity.get('language'),
            contentTypeId: entity.get('content_type'),
            contentId: entity.get('content_id'),
            version: entity.get('version')
        });
    }
}

export default LastContentsWidgetView;
