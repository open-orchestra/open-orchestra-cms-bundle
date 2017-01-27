import AbstractNewBlockComponentListView from './AbstractNewBlockComponentListView'

/**
 * @class NewBlockComponentListView
 */
class NewBlockComponentListView extends AbstractNewBlockComponentListView
{
    /**
     * Initialize
     * @param {BlockComponents} blockComponents
     * @param {string}          language
     * @param {string}          nodeId
     * @param {string}          nodeVersion
     * @param {string}          areaName
     * @param {string}          position
     */
    initialize({blockComponents, language, nodeId, nodeVersion, areaName, position}) {
        super.initialize({blockComponents, language});
        this._nodeId = nodeId;
        this._nodeVersion = nodeVersion;
        this._areaName = areaName;
        this._position = position;
    }

    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBack() {
        return Translator.trans('open_orchestra_backoffice.block.back_to_node');
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        return Backbone.history.generateUrl('showNode', {
            nodeId: this._nodeId,
            language: this._language,
            version: this._nodeVersion
        });
    }

    /**
     * @param {BlockComponent} blockComponent
     * @private
     *
     * @return string
     */
    _getAddBlockUrl(blockComponent) {
        return Backbone.history.generateUrl('newBlockListAvailable', {
            nodeId: this._nodeId,
            nodeVersion: this._nodeVersion,
            nodeLanguage: this._language,
            component: blockComponent.get('component'),
            componentName: blockComponent.get('name'),
            areaName: this._areaName,
            position: this._position
        });
    }
}

export default NewBlockComponentListView;
