import AbstractDataTableView from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin  from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'

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
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.label'),
                orderable: true,
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.type'),
                visibile: true,
                orderable: false
            },
            {
                name: "category.label",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.category'),
                visibile: true,
                orderable: false
            },
            {
                name: "updated_at",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.updated_at'),
                orderable: true,
                visibile: true
            },
            {
                name: "number_use",
                title: Translator.trans('open_orchestra_backoffice.table.shared_block.number_use'),
                orderable: false,
                visibile: true,
                createdCell: this._createUsageBlockLink
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
    _getSyncOptions() {
        return {
            apiContext: 'list-table-shared-block',
            urlParameter: {
                language: this._language
            }
        };
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createUsageBlockLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editSharedBlock', {
            blockId: rowData.get('id'),
            language: rowData.get('language'),
            activateUsageTab: true
        });

        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editSharedBlock', {
            blockId: rowData.get('id'),
            blockLabel: rowData.get('label'),
            language: rowData.get('language')
        });

        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default SharedBlockListView;
