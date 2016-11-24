import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'

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
                visibile: true
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
                name: 'status.label',
                title: Translator.trans('open_orchestra_backoffice.table.node.current_status'),
                orderable: true,
                visibile: true
            }
        ];
    }


    /**
     * @return {Function}
     * @private
     */
    _dataTableAjaxCollection() {
        let collection = this._collection;
        return (request, drawCallback, settings) => {
            settings.jqXHR = collection.fetch({
                urlParameter: {
                    'language': this._language,
                    'siteId': this._siteId
                },
                data: request,
                processData: true,
                success: (collection) => {
                    settings.sAjaxDataProp = "models";

                    return drawCallback(collection);
                }
            });
        }
    }

}

export default NodeListView;
