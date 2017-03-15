import AbstractVersionsListView from '../Versionable/AbstractVersionsListView'

/**
 * @class ContentVersionsListView
 */
class ContentVersionsListView extends AbstractVersionsListView
{
    /**
     * @inheritDoc
     */
    initialize({collection, settings, contentTypeId, language, contentId}) {
        super.initialize({collection: collection, settings: settings});
        this._contentTypeId = contentTypeId;
        this._language = language;
        this._contentId = contentId;
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'content_versions_list';
    }

    /**
     * @param {Object} rowData
     *
     * @private
     */
    _canDelete(rowData) {
        return rowData.get('rights').hasOwnProperty('can_delete_version') && rowData.get('rights').can_delete_version;
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editContent', {
            contentTypeId: rowData.get('content_type'),
            language: rowData.get('language'),
            contentId: rowData.get('content_id'),
            version: rowData.get('version')
        });

        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('manageVersionsContent', {
            contentId: this._contentId,
            contentTypeId: this._contentTypeId,
            language: this._language,
            page : page
        });
    }
}

export default ContentVersionsListView;
