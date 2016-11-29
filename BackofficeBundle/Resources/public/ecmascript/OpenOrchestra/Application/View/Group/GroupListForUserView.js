import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'

/**
 * @class GroupListForUserView
 */
class GroupListForUserView extends AbstractDataTableView
{
    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = {
            'draw.dt table': '_updatePage'
        };
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
                name: "label",
                title: Translator.trans('open_orchestra_backoffice.table.group.label'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: 'checkbox',
                orderable: false,
                createdCell: this._addCheckbox
            }
        ];
    }

    /**
     *
     * @param {Object} td
     *
     * @private
     */
    _addCheckbox(td) {
        $(td).html('<input type="checkbox">');
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _updatePage(event) {
        let api = $(event.target).DataTable();
        let page = api.page.info().page + 1;
    }
}

export default GroupListForUserView;
