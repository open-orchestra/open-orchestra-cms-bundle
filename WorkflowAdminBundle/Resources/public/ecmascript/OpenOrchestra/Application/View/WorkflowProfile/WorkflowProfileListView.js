import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin  from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class WorkflowProfileListView
 */
class WorkflowProfileListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
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
        return 'workflow_profile_list';
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
                title: Translator.trans('open_orchestra_workflow_admin.table.workflow_profile.label'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "description",
                title: Translator.trans('open_orchestra_workflow_admin.table.workflow_profile.description'),
                orderable: false,
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listWorkflowProfile', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editWorkflowProfile', {workflowProfileId: rowData.get('id')});
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
        let workflowProfile = $(event.currentTarget).data();
        workflowProfile.set('delete', $(event.currentTarget).prop('checked'));
    }
}

export default WorkflowProfileListView;
