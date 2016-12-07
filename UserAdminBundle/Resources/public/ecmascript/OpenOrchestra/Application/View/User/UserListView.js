import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class UserListView
 */
class UserListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.events = {
            'change .delete-checkbox' : '_changeDeleteCheckbox'
        };
    }

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
     *
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
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let $checkbox = $('<input>', {type: 'checkbox', id: id, class:'delete-checkbox'});
        $checkbox.data(rowData);
        $(td).append($checkbox);
        $(td).append($('<label>', {for: id}))
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _changeDeleteCheckbox(event) {
        let user = $(event.currentTarget).data();
        user.set('delete', $(event.currentTarget).prop('checked'));
        console.log(user);
    }
}

export default UserListView;
