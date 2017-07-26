import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

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
                title: Translator.trans('open_orchestra_backoffice.table.content_types.linked_to_site'),
                orderable: true,
                visibile: true,
                render: this._translateLinkedToSite
            }
        ];

    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'apiContext': 'list'
        };
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
        let link = Backbone.history.generateUrl('editContentType', {
            'contentTypeId': rowData.get('content_type_id')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @param {Object|string} data
     * @private
     */
    _translateLinkedToSite(data) {
        return Translator.trans('open_orchestra_backoffice.table.' + data);
    }
}

export default ContentTypesListView;
