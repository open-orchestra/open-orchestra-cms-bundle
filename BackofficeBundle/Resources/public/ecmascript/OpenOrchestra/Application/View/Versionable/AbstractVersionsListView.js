import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class AbstractVersionsListView
 */
class AbstractVersionsListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
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
                title: Translator.trans('open_orchestra_backoffice.table.versionable.version_name'),
                orderable: true,
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: 'updated_at',
                title: Translator.trans('open_orchestra_backoffice.table.versionable.updated_at'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: 'created_by',
                title: Translator.trans('open_orchestra_backoffice.table.versionable.created_by'),
                orderable: true,
                visibile: true
            },
            {
                name: 'status.label',
                title: Translator.trans('open_orchestra_backoffice.table.versionable.status_label'),
                orderable: true,
                visibile: true
            }
        ]);

        return columnsDefinition;
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
        throw new TypeError("Please implement abstract method _createEditLink.");
    }
}

export default AbstractVersionsListView;
