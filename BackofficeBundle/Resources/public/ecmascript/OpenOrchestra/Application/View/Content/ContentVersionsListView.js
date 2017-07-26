import AbstractVersionsListView from 'OpenOrchestra/Application/View/Versionable/AbstractVersionsListView'

/**
 * @class ContentVersionsListView
 */
class ContentVersionsListView extends AbstractVersionsListView
{
    /**
     * @inheritDoc
     */
    preinitialize({collection, settings, contentType, language, contentId}) {
        super.preinitialize({collection: collection, settings: settings});
        this._contentType = contentType;
        this._language = language;
        this._contentId = contentId;
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        let columnsDefinition = super.getColumnsDefinition();
        if (false === this._contentType.get('defining_statusable')) {
            let indexStatus = _.findIndex(columnsDefinition, {name : 'status.label'});
            if (-1 !== indexStatus) {
                columnsDefinition.splice(indexStatus, 1);
            }
        }

        return columnsDefinition;
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
            contentTypeId: this._contentType.get("content_type_id"),
            language: this._language,
            page : page
        });
    }
}

export default ContentVersionsListView;
