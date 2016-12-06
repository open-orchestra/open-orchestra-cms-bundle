import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'

/**
 * @class GroupListForUserView
 */
class GroupListForUserView extends AbstractDataTableView
{
    /**
     * @inheritDoc
     */
    preinitialize({collection, blockedGroups, selectedGroups, settings}) {
        super.initialize({collection, settings});
        this._blockedGroups = blockedGroups;
        this._selectedGroups = selectedGroups;
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
                name: 'checkbox',
                orderable: false,
                createdCell: this._addCheckbox.bind(this),
                width: '20px'
            },
            {
                name: "label",
                title: Translator.trans('open_orchestra_backoffice.table.group.label'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: "site.name",
                title: Translator.trans('open_orchestra_backoffice.table.group.site_name'),
                orderable: false,
                visibile: true
            }
        ];
    }

    /**
     *
     * @param {Object} td
     *
     * @private
     */
    _addCheckbox(td, cellData, rowData) {
        let template = this._renderTemplate('Group/modalCellView',
            {
                id: rowData.get('id'),
                is_blocked: (this._blockedGroups.indexOf(rowData.get('id')) > -1),
                is_selected: (this._selectedGroups.indexOf(rowData.get('id')) > -1)
            }
        );
        $(td).html(template);
    }
}

export default GroupListForUserView;
