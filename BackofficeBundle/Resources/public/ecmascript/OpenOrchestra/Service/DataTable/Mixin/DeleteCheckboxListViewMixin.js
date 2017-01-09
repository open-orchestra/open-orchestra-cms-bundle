let DeleteCheckboxListViewMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['change .delete-checkbox'] = '_changeDeleteCheckbox';
    }

    /**
     * @inheritDoc
     */
    _getColumnsDefinitionDeleteCheckbox() {
        return {
                name: "delete",
                orderable: false,
                width: '20px',
                createdCell: this._createCheckbox,
                render: () => { return ''}
            };
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let attributes = {type: 'checkbox', id: id, class:'delete-checkbox'};
        if (rowData.get('rights').hasOwnProperty('can_delete') &&
            !rowData.get('rights').can_delete) {
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
