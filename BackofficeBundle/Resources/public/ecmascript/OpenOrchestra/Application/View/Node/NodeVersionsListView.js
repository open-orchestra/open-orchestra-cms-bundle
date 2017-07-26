import AbstractVersionsListView from 'OpenOrchestra/Application/View/Versionable/AbstractVersionsListView'

/**
 * @class NodeVersionsListView
 */
class NodeVersionsListView extends AbstractVersionsListView
{
    /**
     * @inheritDoc
     */
    initialize({collection, settings, nodeId, language}) {
        super.initialize({collection: collection, settings: settings});
        this._nodeId = nodeId;
        this._language = language;
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'node_versions_list';
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('showNode', {
            language: rowData.get('language'),
            nodeId: rowData.get('node_id'),
            version: rowData.get('version')
        });

        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('manageVersionsNode', {
            nodeId: this._nodeId,
            language: this._language,
            page : page
        });
    }
}

export default NodeVersionsListView;
