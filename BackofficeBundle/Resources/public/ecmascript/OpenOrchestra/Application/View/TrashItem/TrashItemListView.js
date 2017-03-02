import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class TrashItemListView
 */
class TrashItemListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'trash_item_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.trash_item.name'),
                orderable: true,
                orderDirection: 'asc'
            },
            {
                name: "type",
                orderable: true,
                title: Translator.trans('open_orchestra_backoffice.table.trash_item.type')
            },            {
                name: "deleted_at",
                orderable: true,
                title: Translator.trans('open_orchestra_backoffice.table.trash_item.deleted_at')
            }
        ];
    }


    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listTrashItem', {page : page});
    }
}

export default TrashItemListView;
