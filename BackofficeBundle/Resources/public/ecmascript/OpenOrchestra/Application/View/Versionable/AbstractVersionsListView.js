import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class AbstractVersionsListView
 */
class AbstractVersionsListView extends mix(AbstractDataTableView).with(DeleteCheckboxListViewMixin)
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
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: 'created_at',
                title: Translator.trans('open_orchestra_backoffice.table.versionable.created_at'),
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
}

export default AbstractVersionsListView;