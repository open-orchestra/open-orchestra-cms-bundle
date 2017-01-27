import OrchestraView                    from '../OrchestraView'
import BlockView                        from '../Block/BlockView'
import SelectLanguageNodeModalView      from './SelectLanguageNodeModalView'
import MessageCopySharedBlocksModalView from './MessageCopySharedBlocksModalView'

import Nodes                            from '../../Collection/Node/Nodes'
import Node                             from '../../Model/Node/Node'

import Application                      from '../../Application'

/**
 * @class AreaView
 */
class AreaView extends OrchestraView
{
    /**
     * Pre initialize
     */
    preinitialize(options) {
        this.events = {
            'click .copy-blocks' : '_showSelectNodeModal'
        }
    }

    /**
     * Initialize
     * @param {Area} area
     * @param {Node} node
     */
    initialize({area, node}) {
        this._node = node;
        this._area = area;
    }

    /**
     * Render area
     */
    render() {
        let templateAddBlockLink = this._renderTemplate('Block/addBlockLink', {
            node: this._node,
            areaName: this._area.get('name'),
            position: '0'
        });
        this.$el.html(templateAddBlockLink);
        if (0 === this._area.get('blocks').length) {
            if (true === this._node.get('status').get('translation_state')) {
                let templateCopyBlock = this._renderTemplate('Area/copyBlocksButton');
                this.$el.append(templateCopyBlock);
            }
        } else {
            this._renderBlocks(this.$el);
        }

        this.$el.data('area', this._area);

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderBlocks($selector) {
        for (let block of this._area.get('blocks').models) {
            let blockView = new BlockView({block: block, area: this._area, node: this._node});
            $selector.append(blockView.render().$el);
        }
    }

    /**
     * copy block from an other language
     *
     * @private
     */
    _showSelectNodeModal() {
        new Nodes().fetch({
            apiContext: 'list-with-block-in-area',
            urlParameter: {
                nodeId: this._node.get('node_id'),
                siteId: this._node.get('site_id'),
                areaId: this._area.get('name')
            },
            success: (nodes) => {
                let selectLanguageNodeModalView = new SelectLanguageNodeModalView({
                    nodes: nodes,
                    callbackCopyBlock: $.proxy(this._copyBlockFromNode, this)
                });
                Application.getRegion('modal').html(selectLanguageNodeModalView.render().$el);
                selectLanguageNodeModalView.show();
            }
        });
    }

    /**
     *  Copy blocks from area of node
     *
     * @param {Node} node
     */
    _copyBlockFromNode(node) {
        let areaId = this._area.get('name');
        let area = node.getArea(areaId);
        let blocks = area.get('blocks');
        let attributes = {
            'areas' : {}
        };
        attributes.areas[areaId] = area;
        this._node.save(attributes, {
            patch: true,
            urlParameter: {
                nodeId: this._node.get('node_id'),
                version: this._node.get('version'),
                language: this._node.get('language'),
                areaId: this._area.get('name')
            },
            apiContext: 'copy_translated_blocs',
            success: (node, response) => {
                node = new Node(response);
                this._area = node.getArea(areaId);
                this.render();
                let sharedBlocks = blocks.where({'transverse': true});
                if (sharedBlocks.length > 0) {
                    let messageCopySharedBlocksView = new MessageCopySharedBlocksModalView({
                        sharedBlocks: sharedBlocks
                    });
                    Application.getRegion('modal').html(messageCopySharedBlocksView.render().$el);
                    messageCopySharedBlocksView.show();
                }
            }
        });
    }
}

export default AreaView;
