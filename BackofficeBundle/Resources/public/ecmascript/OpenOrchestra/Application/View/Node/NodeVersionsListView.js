import AbstractVersionsListView from '../Versionable/AbstractVersionsListView'

/**
 * @class NodeVersionsListView
 */
class NodeVersionsListView extends AbstractVersionsListView
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'node_versions_list';
    }
}

export default NodeVersionsListView;
