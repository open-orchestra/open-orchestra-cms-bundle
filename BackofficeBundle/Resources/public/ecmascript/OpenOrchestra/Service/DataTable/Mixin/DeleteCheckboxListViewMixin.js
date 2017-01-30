let DeleteCheckboxListViewMixin = (superclass) => class extends superclass {

    /**
     * pre initialize
     *
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['change .delete-checkbox'] = '_changeDeleteCheckbox';
    }

    /**
     * Description of column checkbox
     */
    _getColumnsDefinitionDeleteCheckbox() {
        return {
                name: "delete",
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
        $(td).append($checkbox);
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
