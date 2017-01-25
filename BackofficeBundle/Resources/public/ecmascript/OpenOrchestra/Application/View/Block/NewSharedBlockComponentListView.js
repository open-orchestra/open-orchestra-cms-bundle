import AbstractNewBlockComponentListView from './AbstractNewBlockComponentListView'

/**
 * @class NewSharedBlockComponentListView
 */
class NewSharedBlockComponentListView extends AbstractNewBlockComponentListView
{
    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBack() {
        return Translator.trans('open_orchestra_backoffice.back_to_list');
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        return Backbone.history.generateUrl('listSharedBlock');
    }

    /**
     * @param {BlockComponent} blockComponent
     * @private
     *
     * @return string
     */
    _getAddBlockUrl(blockComponent) {
        return Backbone.history.generateUrl('newSharedBlock', {
            language: this._language,
            component: blockComponent.get('component'),
            name: blockComponent.get('name')
        });
    }
}

export default NewSharedBlockComponentListView;
