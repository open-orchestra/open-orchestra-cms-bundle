import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'

import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import DateFormatter               from '../../../Service/DataFormatter/DateFormatter'
import Application                 from '../../Application'

/**
 * @class TrashItemListView
 */
class TrashItemListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['click .btn-restore:not(.disabled)'] = '_restore';
    }

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
                render: DateFormatter.format,
                title: Translator.trans('open_orchestra_backoffice.table.trash_item.deleted_at')
            },
            {
                name: "restore",
                title: Translator.trans('open_orchestra_backoffice.table.trash_item.restore'),
                orderable: false,
                visibile: true,
                width: '20px',
                createdCell: this._createRestoreIcon,
                render: () => { '' }
            }
        ];
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createRestoreIcon(td, cellData, rowData) {
        let iconClass = 'btn-restore fa fa-undo';
        if (!(rowData.get('rights').hasOwnProperty('can_restore') && rowData.get('rights').can_restore)) {
            iconClass += ' disabled';
        }
        let $icon = $('<i>', {'aria-hidden': 'true', class: iconClass});
        $icon.data(rowData);
        $(td).append($icon);
    }

    /**
     * @param {object} event
     * @private
     */
    _restore(event) {
        let model = $(event.currentTarget).data();
        model = this._collection.findWhere({'id': model.get('id')});
        let type = model.get('type');

        if (typeof Application.getConfiguration().getParameter('restoreModalViews')[type] === 'undefined') {
            throw new TypeError('Unknown restore modal view for the type ' + type );
        }

        let restoreModalViewClass = Application.getConfiguration().getParameter('restoreModalViews')[type];
        let restoreModalView = new restoreModalViewClass({
                model: model,
                listApi: this.api
            }
        );
        Application.getRegion('modal').html(restoreModalView.render().$el);
        restoreModalView.show();
    }


    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listTrashItem', {page : page});
    }
}

export default TrashItemListView;
