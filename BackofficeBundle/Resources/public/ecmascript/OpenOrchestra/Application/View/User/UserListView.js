import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class UserListView
 */
class UserListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
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
            {
                name: "delete",
                orderable: false,
                width: '20px',
                createdCell: this._createCheckbox
            },
            {
                name: "username",
                title: Translator.trans('open_orchestra_backoffice.table.users.username'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "groups",
                title: Translator.trans('open_orchestra_backoffice.table.users.groups'),
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
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        cellData = $('<a>',{
            text: cellData,
            href: '#'
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
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let $checkbox = $('<input>', {type: 'checkbox', id: id});
        $checkbox.on('change', (event) => {
            rowData.set('delete', $(event.currentTarget).prop('checked'));
        });
        $(td).append($checkbox);
        $(td).append($('<label>', {for: id}))
    }
}

export default UserListView;
