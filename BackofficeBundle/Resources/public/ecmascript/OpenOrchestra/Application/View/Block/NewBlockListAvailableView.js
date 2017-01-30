import OrchestraView    from '../OrchestraView'
import ApplicationError from '../../../Service/Error/ApplicationError'
import Node             from '../../Model/Node/Node'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewBlockListAvailableView
 */
class NewBlockListAvailableView extends OrchestraView
{
    /**
     * Pre initialize
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = {
            'click .shared-block': '_addSharedBlock'
        };
    }

    /**
     * Initialize
     * @param {Blocks} blocks
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     * @param {String} component
     * @param {String} componentName
     * @param {String} areaName
     * @param {String} position
     */
    initialize({blocks, nodeId, nodeLanguage, nodeVersion, component, componentName, areaName, position}) {
        this._blocks = blocks;
        this._nodeId = nodeId;
        this._nodeLanguage = nodeLanguage;
        this._nodeVersion = nodeVersion;
        this._componentName = componentName;
        this._component = component;
        this._areaName = areaName;
        this._position = position;
    }

    /**
     * Render list block available
     */
    render() {
        let template = this._renderTemplate('Block/newBlockListAvailableView',
            {
                blocks: this._blocks,
                nodeId: this._nodeId,
                nodeLanguage: this._nodeLanguage,
                nodeVersion: this._nodeVersion,
                component: this._component,
                componentName: this._componentName,
                areaName: this._areaName,
                position: this._position
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * @param {Object} event
     * @private
     */
    _addSharedBlock(event) {
        let blockId = $(event.currentTarget).attr('data-block-id');
        let block = this._blocks.findWhere({'id': blockId});
        if (typeof block === 'undefined') {
            throw new ApplicationError('Block not found');
        }
        new Node({id: this._nodeId}).save({}, {
            apiContext: 'add_block',
            urlParameter: {
                nodeId: this._nodeId,
                language: this._nodeLanguage,
                version: this._nodeVersion,
                blockId: blockId,
                areaId: this._areaName,
                position: this._position
            },
            success: () => {
                let message = new FlashMessage(Translator.trans('open_orchestra_backoffice.block.creation'), 'success');
                FlashMessageBag.addMessageFlash(message);
                let url = Backbone.history.generateUrl('showNode', {
                    nodeId: this._nodeId,
                    language: this._nodeLanguage,
                    version: this._nodeVersion
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default NewBlockListAvailableView;
