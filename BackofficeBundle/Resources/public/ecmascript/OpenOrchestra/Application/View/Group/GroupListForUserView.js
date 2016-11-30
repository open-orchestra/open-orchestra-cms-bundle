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
    _addCheckbox(td, cellData) {
        console.log(cellData);
        let cell = '' +
            '<div class="form-group ">' +
            '    <div class="col-md-10 switch-button">' +
            '        <span>' + Translator.trans('open_orchestra_backoffice.form.swchoff.off') + '</span>' +
            '        <label class="switch" for="oo_user[editAllowed]">' +
            '            <input type="checkbox" value="1" name="oo_user[editAllowed]" id="oo_user[editAllowed]">' +
            '            <div class="slider"></div>' +
            '        </label>' +
            '        <span>' + Translator.trans('open_orchestra_backoffice.form.swchoff.on') + '</span>' +
            '    </div>' +
            '</div>';
        $(td).html(cell);
    }
}

export default GroupListForUserView;
