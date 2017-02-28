import AbstractNewBlockFormView from './AbstractNewBlockFormView'
import FlashMessageBag          from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage             from '../../../Service/FlashMessage/FlashMessage'

/**
 * @class NewBlockFormView
 */
class NewBlockFormView extends AbstractNewBlockFormView
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {string} name
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     * @param {string} component
     * @param {string} areaName
     * @param {string} position
     */
    initialize({form, name, nodeId, nodeLanguage, nodeVersion, component, areaName, position}) {
        super.initialize({form, name});
        this._nodeId = nodeId;
        this._nodeLanguage = nodeLanguage;
        this._nodeVersion = nodeVersion;
        this._component = component;
        this._areaName = areaName;
        this._position = position;
    }

    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBackList() {
        return Translator.trans('open_orchestra_backoffice.block.back_to_node');
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        return Backbone.history.generateUrl('newBlockListAvailable',{
            nodeId: this._nodeId,
            nodeLanguage: this._nodeLanguage,
            nodeVersion: this._nodeVersion,
            component: this._component,
            componentName: this._name,
            areaName: this._areaName,
            position: this._position
        });
    }

    /**
     * @inheritdoc
     */
    _getUrlButtonBackList() {
        return Backbone.history.generateUrl('showNode',{
            nodeId: this._nodeId,
            language: this._nodeLanguage,
            version: this._nodeVersion
        });
    }

    /**
     * @inheritdoc
     */
    getStatusCodeForm(event) {
        return {
            '422': $.proxy(this.refreshRender, this),
            '201': $.proxy(this._redirectShowNode, this)
        };
    }

    /**
     * Redirect to node view
     *
     * @private
     */
    _redirectShowNode() {
        let url = Backbone.history.generateUrl('showNode', {
            nodeId: this._nodeId,
            language: this._nodeLanguage,
            version: this._nodeVersion
        });
        let message = new FlashMessage(Translator.trans('open_orchestra_backoffice.block.creation'), 'success');
        FlashMessageBag.addMessageFlash(message);
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
}

export default NewBlockFormView;
