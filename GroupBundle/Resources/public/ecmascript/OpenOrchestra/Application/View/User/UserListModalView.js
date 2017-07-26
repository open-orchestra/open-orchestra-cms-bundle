import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class UserListModalView
 */
class UserListModalView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{

    /**
     * @param {Object} collection
     * @param {Object} selected
     * @param {Array}  settings
     */
    initialize({collection, selected, settings}) {
        super.initialize({collection, settings});
        this._selected = selected;
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
                name: "add",
                orderable: false,
                visibile: true,
                width: '20px',
                createdCell: $.proxy(this._createAddCheckbox, this)
            },
            {
                name: "username",
                title: Translator.trans('open_orchestra_group.table.members_list.username'),
                orderable: true,
                visibile: true,
            },
            {
                name: "email",
                title: Translator.trans('open_orchestra_group.table.members_list.email'),
                orderable: false,
                visibile: true,
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.getFragment();
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createAddCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let $checkbox = $('<input>', {
            type: 'checkbox',
            id: id,
            class: 'add-checkbox',
            name: 'user',
            value: rowData.get('id')
        });
        if(typeof this._selected.find(function(item){return item.get('id') === rowData.get('id');}) != 'undefined') {
            $checkbox.attr({
                disabled: "disabled",
                checked: "checked"
            });
        }
        $checkbox.data(rowData);
        $(td).html($checkbox);
        $(td).append($('<label>', {for: id}));
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

export default UserListModalView;
