import ModalView          from '../../../Service/Modal/View/ModalView'
import OrchestraModalView from '../Block/BlockView'
import ApplicationError   from '../../../Service/Error/ApplicationError'

/**
 * @class SelectLanguageNodeModalView
 */
class SelectLanguageNodeModalView extends ModalView
{
    /**
     * Pre initialize
     *
     * @param options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click .copy-language'] = '_selectNodeCopy'
    }

    /**
     * Initialize
     * @param {Nodes}    nodes
     * @param {Function} callbackCopyBlock
     */
    initialize({nodes, callbackCopyBlock}) {
        this._nodes = nodes;
        this._callbackCopyBlock = callbackCopyBlock;
    }

    /**
     * Render modal select node copy block
     */
    render() {
        let template = this._renderTemplate('Area/selectLanguageNodeModalView', {
            nodes: this._nodes
        });
        this.$el.append(template);

        return this;
    }

    /**
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _selectNodeCopy(event) {
        let language = $(event.currentTarget).attr('data-language');
        let node = this._nodes.findWhere({language: language});

        if (typeof node === 'undefined') {
            throw new ApplicationError('Node with language ' + language + ' for copy not found');
        }
        this._callbackCopyBlock(node);
        this.hide();
    }
}

export default SelectLanguageNodeModalView;
