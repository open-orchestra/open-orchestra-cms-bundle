import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class ContentVersionsListView
 */
class ContentVersionsListView extends mix(AbstractDataTableView).with(DeleteCheckboxListViewMixin)
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

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        let columnsDefinition = [];
        if (this._collection.length > 1) {
            columnsDefinition.push(this._getColumnsDefinitionDeleteCheckbox());
        }
        columnsDefinition = columnsDefinition.concat([
            {
                name: 'version_name',
                title: Translator.trans('open_orchestra_backoffice.table.contents.version_name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: 'created_at',
                title: Translator.trans('open_orchestra_backoffice.table.contents.created_at'),
                orderable: true,
                visibile: true
            },
            {
                name: 'status.label',
                title: Translator.trans('open_orchestra_backoffice.table.contents.status_label'),
                orderable: true,
                visibile: true
            }
        ]);

        return columnsDefinition;
    }
}

export default ContentVersionsListView;
