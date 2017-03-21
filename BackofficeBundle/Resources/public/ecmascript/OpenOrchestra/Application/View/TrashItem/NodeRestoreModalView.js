import AbstractRestoreModalView from './AbstractRestoreModalView'
import Application              from '../../Application'

/**
 * @class NodeRestoreModalView
 */
class NodeRestoreModalView extends AbstractRestoreModalView
{
    /**
     * @private
     */
    _validRestoreAndEdit() {
        this._model.destroy({
             success: () => {
                 let url = Backbone.history.generateUrl('showNode', {
                     nodeId: this._model.get('entity_id'),
                     language : Application.getContext().user.language.contribution
                 });
                 Backbone.history.navigate(url, true);
                 this.hide();
             }
        });
    }
}

export default NodeRestoreModalView;