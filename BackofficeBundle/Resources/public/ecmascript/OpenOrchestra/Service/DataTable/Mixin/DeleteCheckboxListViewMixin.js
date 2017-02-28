let DeleteCheckboxListViewMixin = (superclass) => class extends superclass {

    /**
     * pre initialize
     *
     * @param {Object} options
     */
    preinitialize(options) {
        options.settings['headerCallback'] = $.proxy(this._createHeaderDeleteCheckbox, this);
        super.preinitialize(options);
        this._columnName = 'delete';
        this.events = this.events || {};
        this.events['change .delete-checkbox'] = '_changeDeleteCheckbox';
        this.events['change #select-all-checkbox'] = '_changeSelectAllDeleteCheckbox';
    }

    /**
     * Description of column checkbox
     */
    _getColumnsDefinitionDeleteCheckbox() {
        return {
                name: this._columnName,
                orderable: false,
                visibile: true,
                width: '20px',
                createdCell: this._createDeleteCheckbox,
                render: () => { return ''}
            };
    }

    /**
     * @param {Object} rowData
     *
     * @private
     */
    _canDelete(rowData) {
        return rowData.get('rights').hasOwnProperty('can_delete') && rowData.get('rights').can_delete;
    }

    /**
     * @param {Object} thead
     */
    _createHeaderDeleteCheckbox(thead) {
        let index = this.$table.DataTable().column(this._columnName+':name').index('visible');
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
    _changeSelectAllDeleteCheckbox(event) {
        let index = this.$table.DataTable().column(this._columnName+':name').index('visible');
        let checked = $(event.currentTarget).prop('checked');
        let checkbox = $('tr td:nth-child('+(index+1)+') input.delete-checkbox:enabled', this.$table);

        checkbox.prop('checked', checked).change();
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createDeleteCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let attributes = {type: 'checkbox', id: id, class:'delete-checkbox'};
        if (!this.data('context')._canDelete(rowData)) {
            attributes.disabled = 'disabled';
        }

        let $checkbox = $('<input>', attributes);
        $checkbox.data(rowData);
        $(td).html($checkbox);
        $(td).append($('<label>', {for: id}));
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _changeDeleteCheckbox(event) {
        let model = $(event.currentTarget).data();
        model.set('delete', $(event.currentTarget).prop('checked'));
    }
};

export default DeleteCheckboxListViewMixin;
