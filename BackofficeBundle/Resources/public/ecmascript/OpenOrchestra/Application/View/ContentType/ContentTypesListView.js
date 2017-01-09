import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class ContentTypesListView
 */
class ContentTypesListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'content_type_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.content_types.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "content_type_id",
                title: Translator.trans('open_orchestra_backoffice.table.content_types.content_type_id'),
                orderable: true,
                visibile: true
            },
            {
                name: "linked_to_site",
                title: Translator.trans('open_orchestra_backoffice.table.content_types.linked_to_site.label'),
                orderable: true,
                visibile: true,
                render: this._translateLinkedToSite
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listContentType', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = '';
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
        if (rowData.get('rights').can_delete) {
            super._createCheckbox(td, cellData, rowData)
        }
    }

    /**
     * @param {Object|string} data
     * @param {string}        type
     * @param {Object}        full
     * @param {Object}        meta
     * @private
     */
    _translateLinkedToSite(data,type,full,meta) {
        return Translator.trans('open_orchestra_backoffice.table.content_types.linked_to_site.'+data);
    }
}

export default ContentTypesListView;
