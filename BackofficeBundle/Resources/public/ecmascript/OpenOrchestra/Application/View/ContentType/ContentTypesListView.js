import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class ContentTypesListView
 */
class ContentTypesListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
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
