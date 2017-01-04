import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class SharedBlockListView
 */
class SharedBlockListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritdoc
     */
    initialize({collection, language, settings}) {
        super.initialize({collection: collection, settings: settings});
        this._language = language;
    }
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'shared_block_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "label",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "category",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.type'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: "updated_at",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.updated_at'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listSharedBlock', {language: this._language, page : page});
    }

    /**
     * @inheritDoc
     */
    _getSyncUrlParameter() {
        return {
            language: this._language
        };
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editBlock', {blockId: rowData.get('id')});
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default SharedBlockListView;
