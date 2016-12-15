import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin  from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class StatusListView
 */
class StatusListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change .delete-checkbox'] = '_changeDeleteCheckbox';
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'status_list';
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
                createdCell: this._createCheckbox
            },
            {
                name: "label",
                title: Translator.trans('open_orchestra_workflow_admin.table.statuses.label'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "properties",
                title: Translator.trans('open_orchestra_workflow_admin.table.statuses.specificities'),
                orderable: false,
                visibile: true,
                createdCell: this._agglomerateProperties
            },
            {
                name: "display_color",
                title: Translator.trans('open_orchestra_workflow_admin.table.statuses.display_color'),
                orderable: false,
                visibile: true,
                createdCell: this._formatColor
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listStatus', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editStatus', {
            statusId: rowData.get('id')
        });
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
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;

        let attributes = {type: 'checkbox', id: id, class:'delete-checkbox'};
        if (!rowData.get('rights').can_delete) {
            attributes.disabled = 'disabled';
        }

        let $checkbox = $('<input>', attributes);
        $checkbox.data(rowData);
        $(td).append($checkbox);
        $(td).append($('<label>', {for: id}))
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _changeDeleteCheckbox(event) {
        let status = $(event.currentTarget).data();
        status.set('delete', $(event.currentTarget).prop('checked'));
    }

   /**
    *
    * @param {Object} td
    * @param {Object} cellData
    * @param {Object} rowData
    *
    * @private
    */
    _formatColor(td, cellData, rowData) {
        $(td).html('<span style="color:' + rowData.get('code_color') + '">' + rowData.get('display_color') + '</span>');
    }

    /**
    *
    * @param {Object} td
    * @param {Object} cellData
    * @param {Object} rowData
    *
    * @private
    */
    _agglomerateProperties(td, cellData, rowData) {
        let attributes = [];

        if (rowData.get('initial_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.initial_state')); 
        }
        if (rowData.get('translation_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.translation_state')); 
        }
        if (rowData.get('published_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.published_state')); 
        }
        if (rowData.get('auto_publish_from_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.auto_publish_from_state')); 
        }
        if (rowData.get('auto_unpublish_to_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.auto_unpublish_to_state')); 
        }

        $(td).html(attributes.join(', '));
    }
}

export default StatusListView;
