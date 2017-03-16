import AbstractRestoreModalView from './AbstractRestoreModalView'
import Content                  from '../../Model/Content/Content'
import Application              from '../../Application'

/**
 * @class ContentRestoreModalView
 */
class ContentRestoreModalView extends AbstractRestoreModalView
{
    /**
     * @private
     */
    _getEditModelUrl() {
        new Content({id: this._model.get('entity_id') }).fetch({
            success: (content) => {
                console.log(content);
            }
        });
    }

    /**
     * @private
     */
    _validRestoreAndEdit() {
        let content = new Content({id: this._model.get('entity_id') });
        $.when(
            this._model.destroy(),
            content.fetch()
        ).done( () => {
            let url = Backbone.history.generateUrl('editContent', {
                contentTypeId: content.get('content_type'),
                contentId: content.get('content_id'),
                language: Application.getContext().user.language.contribution
            });
            Backbone.history.navigate(url, true);
            this.hide();
        });
    }
}

export default ContentRestoreModalView;
