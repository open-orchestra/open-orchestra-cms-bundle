import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class ContentListView
 */
class ContentListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'content_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.contents.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "content_id",
                title: Translator.trans('open_orchestra_backoffice.table.contents.content_id'),
                orderable: false,
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listContent', {contentTypeId: this._settings.contentTypeId, contentTypeName: this._settings.contentTypeName, page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        cellData = $('<a>',{
            text: cellData,
            href: '#'
        });

        $(td).html(cellData)
    }
}

export default ContentListView;
