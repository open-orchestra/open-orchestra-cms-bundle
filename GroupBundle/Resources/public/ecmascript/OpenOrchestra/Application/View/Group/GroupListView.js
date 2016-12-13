import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin  from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class GroupListView
 */
class GroupListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change .delete-checkbox'] = '_clickDeleteCheckbox';
        this.events['click .clone-icon'] = '_clickDuplicateIcon';
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'group_list';
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
                createdCell: this._createDeleteCheckbox
            },
            {
                name: "label",
                title: Translator.trans('open_orchestra_group.table.groups.label'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "nbr_users",
                title: Translator.trans('open_orchestra_group.table.groups.nbr_users'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
            },
            {
                name: "site.name",
                title: Translator.trans('open_orchestra_group.table.groups.site_name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: "duplicate",
                orderable: false,
                width: '20px',
                createdCell: this._createDuplicateIcon
            },
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listGroup', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = '';/*Backbone.history.generateUrl('editGroup', {
            groupId: rowData.get('id')
        });*/
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
    _createDeleteCheckbox(td, cellData, rowData) {
        let $cell = $('<div>');
        if (rowData.get('rights').can_delete) {
            let id = 'checkbox' + rowData.cid;
            let $checkbox = $('<input>', {type: 'checkbox', id: id, class:'delete-checkbox'});
            $checkbox.data(rowData);
            $cell.append($checkbox);
            $cell.append($('<label>', {for: id}))
        }
        $(td).append($cell);
    }

    /**
    *
    * @param {Object} td
    * @param {Object} cellData
    * @param {Object} rowData
    *
    * @private
    */
   _createDuplicateIcon(td, cellData, rowData) {
       let $icon = $('<i>', {'aria-hidden': 'true', class:'clone-icon fa fa-clone'});
       $icon.data(rowData);
       $(td).append($icon);
   }

    /**
     * @param {Object} event
     *
     * @private
     */
    _clickDeleteCheckbox(event) {
        let group = $(event.currentTarget).data();
        group.set('delete', $(event.currentTarget).prop('checked'));
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _clickDuplicateIcon(event) {
        let group = $(event.currentTarget).data();
        group = this._collection.findWhere({'id': group.get('id')});
        
        group.sync('create', group, {
            success: () => {
                this.api.draw(false);
            }
        });
    }
}

export default GroupListView;
