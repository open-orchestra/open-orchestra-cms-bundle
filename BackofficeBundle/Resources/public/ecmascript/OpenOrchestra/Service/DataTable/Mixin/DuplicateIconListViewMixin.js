let DuplicateIconListViewMixin = (superclass) => class extends superclass {

    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = this.events || {};
        this.events['click .duplicate-icon'] = '_clickDuplicateIcon';
    }

    /**
     * @inheritDoc
     */
    _getColumnsDefinitionDuplicateIcon() {
        return {
                name: "duplicate",
                title: Translator.trans('open_orchestra_backoffice.table.duplicate'),
                orderable: false,
                visibile: true,
                width: '20px',
                createdCell: this._createDuplicateIcon,
                render: () => { return ''}
            };
    }

    /**
     * @param {Object} rowData
     *
     * @private
     */
    _canDuplicate(rowData) {
        return rowData.get('rights').hasOwnProperty('can_create') && rowData.get('rights').can_create;
    }
    
    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createDuplicateIcon(td, cellData, rowData) {
        if (this.data('context')._canDuplicate(rowData)) {
            let $icon = $('<i>', {'aria-hidden': 'true', class:'duplicate-icon fa fa-clone'});
            $icon.data(rowData);
            $(td).append($icon);
        }
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _clickDuplicateIcon(event) {
        let model = $(event.currentTarget).data();
        model = this._collection.findWhere({'id': model.get('id')});

        model.sync('create', model, {
            success: () => {
                this.api.draw(false);
            }
        });
    }
};

export default DuplicateIconListViewMixin;
