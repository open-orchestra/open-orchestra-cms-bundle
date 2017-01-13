import NodeListView from './NodeListView'

/**
 * @class NodeUsageBlockListView
 */
class NodeUsageBlockListView extends NodeListView
{
    /**
     * @inheritdoc
     */
    initialize({collection, language, siteId, blockId, settings}) {
        super.initialize({collection, language, siteId, settings});
        this._blockId = blockId;
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'node_usage_block_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        let columnsDefinition = super.getColumnsDefinition();
        columnsDefinition.push(
            {
                name: 'version',
                title: Translator.trans('open_orchestra_backoffice.table.node.version'),
                orderable: true,
                visibile: true
            }
        );

        return columnsDefinition;
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'context': 'usage-block',
            'urlParameter': {
                'language': this._language,
                'siteId': this._siteId,
                'blockId': this._blockId
            }
        };
    }
}

export default NodeUsageBlockListView;
