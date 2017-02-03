import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import NodeVersionsListView   from './NodeVersionsListView'
import FlashMessageBag        from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage           from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NodeVersionsView
 */
class NodeVersionsView extends AbstractCollectionView
{
    /**
     * @param {OrchestraCollection} collection
     * @param {Node}              node
     */
    initialize({collection, node}) {
        super.initialize({collection: collection});
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
            collection: this._collection
        });
        this.$el.html(template);

        this._listView = new NodeVersionsListView({
            collection: this._collection,
            settings: {
                serverSide: false,
                processing: false,
                data: this._collection.models
            }
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
            urlParameter: {
                nodeId: this._node.get('node_id'),
                language: this._node.get('language')
            },
            success: () => {
                let message = new FlashMessage(Translator.trans('open_orchestra_backoffice.versionable.success_remove'), 'success');
                FlashMessageBag.addMessageFlash(message);

                let url = Backbone.history.generateUrl('showNode', {
                    nodeId: this._node.get('node_id'),
                    language: this._node.get('language')
                });
                if (url === Backbone.history.fragment) {
                    Backbone.history.loadUrl(url);
                } else {
                    Backbone.history.navigate(url, true);
                }
            }
        });
    }
}

export default NodeVersionsView;
