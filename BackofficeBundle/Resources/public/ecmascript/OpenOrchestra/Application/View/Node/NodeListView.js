import AbstractDataTableView from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import StatusFormatter       from 'OpenOrchestra/Service/DataFormatter/StatusFormatter'

/**
 * @class NodeListView
 */
class NodeListView extends AbstractDataTableView
{
    /**
     * @inheritdoc
     */
    initialize({collection, language, siteId, settings}) {
        super.initialize({collection, settings});
        this._language = language;
        this._siteId = siteId;
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'node_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: 'name',
                title: Translator.trans('open_orchestra_backoffice.table.node.title'),
                orderable: true,
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: 'created_by',
                title: Translator.trans('open_orchestra_backoffice.table.node.author'),
                orderable: true,
                visibile: true
            },
            {
                name: 'updated_at',
                title: Translator.trans('open_orchestra_backoffice.table.node.updated_at'),
                orderable: true,
                visibile: true,
                orderDirection: 'desc'
            },
            {
                name: 'status',
                title: Translator.trans('open_orchestra_backoffice.table.node.current_status'),
                orderable: true,
                visibile: true,
                createdCell: this._displayStatus
            }
        ];
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        if (true === rowData.get('status').get('translation_state')) {
            cellData = '[ ' + cellData + ' ]';
        }
        if (true === rowData.get('rights').can_read) {
            let link = Backbone.history.generateUrl('editNode', {
                language: rowData.get('language'),
                nodeId: rowData.get('node_id'),
                version: rowData.get('version')
            });
            cellData = $('<a>',{
                text: cellData,
                href: '#'+link
            });
        }

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
    _displayStatus(td, cellData, rowData) {
       $(td).html(StatusFormatter.format(cellData));
   }

   /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'apiContext': 'list',
            'urlParameter': {
                'language': this._language,
                'siteId': this._siteId
            }
        };
    }
}

export default NodeListView;
