import AbstractCollectionView from 'OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import NodeVersionsListView   from 'OpenOrchestra/Application/View/Node/NodeVersionsListView'

/**
 * @class NodeVersionsView
 */
class NodeVersionsView extends AbstractCollectionView
{
    /**
     * @param {OrchestraCollection} collection
     * @param {Object}              settings
     * @param {array}               siteLanguages
     * @param {Node}                node
     */
    initialize({collection, settings, siteLanguages, node}) {
        super.initialize({collection: collection, settings: settings});
        this._siteLanguages = siteLanguages;
        this._node = node;
    }

    /**
     * @inheritDoc
     */
    _toggleButtonDelete() {
        super._toggleButtonDelete();
        let models = this._collection.where({'delete': true});
        if (models.length >= this._collection.length) {
            $('.btn-delete', this.$el).addClass('disabled');
        }
    }

    /**
     * Render node versions
     */
    render() {
        let template = this._renderTemplate('Node/nodeVersionsView', {
            collection: this._collection,
            siteLanguages: this._siteLanguages,
            node: this._node
        });
        this.$el.html(template);

        let settings = $.extend(true, this._settings, {
                serverSide: false,
                processing: false,
                data: this._collection.models
            }
        );
        this._listView = new NodeVersionsListView({
            collection: this._collection,
            settings: settings,
            nodeId: this._node.get('node_id'),
            language: this._node.get('language')
        });
        $('.node-versions-list', this.$el).html(this._listView.render().$el);

        return this;
    }

    /**
     * Remove
     *
     * @private
     */
    _remove() {
        if (null === this._listView) {
            throw TypeError("Parameter listView should be an instance of AbstractDataTableView");
        }
        let models = this._collection.where({'delete': true});
        this._collection.destroyModels(models, {
            success: () => {
                this._listView.api.clear();
                this._listView.api.rows.add(this._collection.models);
                this._listView.api.draw();
                this._toggleButtonDelete();
            }
        });
    }
}

export default NodeVersionsView;
