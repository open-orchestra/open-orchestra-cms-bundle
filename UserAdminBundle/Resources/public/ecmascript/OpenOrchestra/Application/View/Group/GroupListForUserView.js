import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'

/**
 * @class GroupListForUserView
 */
class GroupListForUserView extends AbstractDataTableView
{
    /**
     * @inheritDoc
     */
    preinitialize(opions) {
        super.preinitialize(opions);
        this.events = {
            'change #select-all-checkbox': '_changeSelectAllCheckbox'
        };
    }

    /**
     * @inheritDoc
     */
    initialize({collection, blockedGroups, selectedGroups, settings}) {
        settings = settings || {};
        settings['headerCallback'] = $.proxy(this._createHeaderCheckbox, this);
        super.initialize({collection: collection, settings: settings});
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
                visibile: true,
                createdCell: this._addCheckbox.bind(this),
                width: '20px',
                render: () => { return ''}
            },
            {
                name: "label",
                title: Translator.trans('open_orchestra_user_admin.table.groups.name'),
                orderable: true,
                visibile: true
            },
            {
                name: "site.name",
                title: Translator.trans('open_orchestra_user_admin.table.groups.site_name'),
                orderable: false,
                visibile: true
            }
        ];
    }

    /**
     * @param {Object} thead
     */
    _createHeaderCheckbox(thead) {
        let index = this.$table.DataTable().column('checkbox:name').index('visible');
        let $headerColumn = $(thead).find('th').eq(index);

        let id = 'select-all-checkbox';
        let attributes = {type: 'checkbox', id: id};
        let $checkbox = $('<input>', attributes);
        $headerColumn.html($checkbox);
        $headerColumn.append($('<label>', {for: id}));
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _changeSelectAllCheckbox(event) {
        let index = this.$table.DataTable().column('checkbox:name').index('visible');
        let checked = $(event.currentTarget).prop('checked');
        let checkbox = $('tr td:nth-child('+(index+1)+') input[type="checkbox"]:enabled', this.$table);

        checkbox.prop('checked', checked).change();
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
