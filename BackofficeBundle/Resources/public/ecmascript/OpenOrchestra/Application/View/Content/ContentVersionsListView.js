import AbstractVersionsListView from '../Versionable/AbstractVersionsListView'

/**
 * @class ContentVersionsListView
 */
class ContentVersionsListView extends AbstractVersionsListView
{
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
}

export default ContentVersionsListView;
