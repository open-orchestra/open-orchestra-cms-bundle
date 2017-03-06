import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class StatusListView
 */
class StatusListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
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
            this._getColumnsDefinitionDeleteCheckbox(),
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
                render: this._getAgglomeratedProperties
            },
            {
                name: "display_color",
                title: Translator.trans('open_orchestra_workflow_admin.table.statuses.display_color'),
                orderable: false,
                visibile: true,
                render: this._getFormatedColor
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
            statusId: rowData.get('id'),
            name: rowData.get('label')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
    *
    * @param {Object} data
    * @param {string} type
    * @param {Status} full
    * @param {Object} meta
    *
    * @private
    */
    _getAgglomeratedProperties(data, type, full, meta) {
        let attributes = [];

        if (full.get('initial_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.initial_state'));
        }
        if (full.get('translation_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.translation_state'));
        }
        if (full.get('published_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.published_state'));
        }
        if (full.get('auto_publish_from_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.auto_publish_from_state'));
        }
        if (full.get('auto_unpublish_to_state')) {
            attributes.push(Translator.trans('open_orchestra_workflow_admin.status.auto_unpublish_to_state'));
        }

        return attributes.join(', ');
    }

    /**
    *
    * @param {Object} data
    * @param {string} type
    * @param {Status} full
    * @param {Object} meta
    *
    * @private
    */
    _getFormatedColor(data, type, full, meta) {
        return '<span class="workflow-' + full.get('code_color') + '">' + full.get('display_color') + '</span>';
    }

    /**
     * Return options used to fetch collection
     *
     * @returns {{}}
     * @private
     */
    _getSyncOptions() {
        return {apiContext: 'table'};
    }
}

export default StatusListView;
