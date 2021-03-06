import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import CsvFormatter                from 'OpenOrchestra/Service/DataFormatter/CsvFormatter'

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
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "groups",
                title: Translator.trans('open_orchestra_user_admin.table.users.groups'),
                orderable: false,
                visibile: true,
                createdCell: this._displayGroupList
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

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _displayGroupList(td, cellData, rowData) {
        $(td).html(CsvFormatter.format(cellData));
    }


    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            apiContext: 'user_list'
        };
    }
}

export default UserListView;
