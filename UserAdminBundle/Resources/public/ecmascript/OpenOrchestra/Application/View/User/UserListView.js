import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class UserListView
 */
class UserListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'user_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "username",
                title: Translator.trans('open_orchestra_user_admin.table.users.username'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "groups",
                title: Translator.trans('open_orchestra_user_admin.table.users.groups'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listUser', {page : page});
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editUser', {
            userId: rowData.get('id')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default UserListView;
