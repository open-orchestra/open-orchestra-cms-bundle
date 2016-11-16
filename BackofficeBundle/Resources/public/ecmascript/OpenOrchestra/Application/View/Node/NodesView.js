import Application   from '../../Application'
import OrchestraView from '../OrchestraView'
import NodesTreeView from './NodesTreeView'
import NodeListView  from './NodeListView'
import NodesTree     from '../../Collection/Node/NodesTree'
import Nodes         from '../../Collection/Node/Nodes'

/**
 * @class NodesView
 */
class NodesView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit': '_search',
            'click .search-engine button.reset': '_reset'
        }
    }

    /**
     * Initialize
     * @param {Statuses} statuses
     * @param {string}   language
     * @param {array}    siteLanguages
     * @param {string}   siteId
     */
    initialize({statuses, language, siteLanguages, siteId}) {
        this._statuses = statuses;
        this._language = language;
        this._siteLanguages = siteLanguages;
        this._siteId = siteId;
    }

    /**
     * Render nodes view
     */
    render() {
        let template = this._renderTemplate('Node/nodesView',
            {
                statuses: this._statuses.models,
                language: this._language,
                siteLanguages: this._siteLanguages
            }
        );
        this.$el.html(template);

        this._nodesRegion = {
            'nodesList' : $('.nodes-list', this.$el),
            'nodesTree' : $('.nodes-tree', this.$el)
        };
        this._diplayLoader(this._nodesRegion.nodesList);
        this._nodesRegion.nodesList.hide();

        this._treeView = this._initializeTree();
        this._listView = this._initializeList();

        return this;
    }

    /**
     * Search node in list
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();
        if (false === this._nodesRegion.nodesList.is(':visible')) {
            this._nodesRegion.nodesList.show();
            this._nodesRegion.nodesTree.hide();
            $('button.reset', this.$el).removeClass('hidden');
        }

        let formData = $('form.search-engine', this.$el).serializeArray();
        let filters = {};
        for (let data of formData) {
            filters[data.name] = data.value;
        }

        this._listView.filter(filters);

        return false;
    }

    /**
     * Reset search
     * @returns {boolean}
     * @private
     */
    _reset() {
        this._nodesRegion.nodesList.hide();
        this._nodesRegion.nodesTree.show();
        $('button.reset', this.$el).addClass('hidden');
        $('form.search-engine', this.$el).trigger('reset');

        return false;
    }

    /**
     * @returns {NodesTreeView}
     *
     * @private
     */
    _initializeTree() {
        let collectionNodesTree = new NodesTree();
        let treeView = new NodesTreeView({
            statuses: this._statuses,
            nodesTree : collectionNodesTree,
            language: this._language}
        );
        collectionNodesTree.fetch({
             urlParameter: {
                 'language': this._language,
                 'siteId': this._siteId
             },
             success: () => {
                 this._nodesRegion.nodesTree.html(treeView.render().$el);
             }
        });

        return treeView
    }

    /**
     * @returns {NodeListView}
     *
     * @private
     */
    _initializeList() {
        let collection = new Nodes();
        let listView = new NodeListView({
            collection: collection,
            language: this._language,
            siteId: this._siteId
        });
        this._nodesRegion.nodesList.html(listView.render().$el);

        return listView;
    }
}

export default NodesView;
