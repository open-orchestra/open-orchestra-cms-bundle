import AbstractRestoreModalView from 'OpenOrchestra/Application/View/TrashItem/AbstractRestoreModalView'
import Content                  from 'OpenOrchestra/Application/Model/Content/Content'
import Application              from 'OpenOrchestra/Application/Application'

/**
 * @class ContentRestoreModalView
 */
class ContentRestoreModalView extends AbstractRestoreModalView
{
    /**
     * @private
     */
    _validRestoreAndEdit() {
        let content = new Content({id: this._model.get('entity_id') });
        $.when(
            this._model.destroy(),
            content.fetch()
        ).done( () => {
            let user = Application.getContext().get('user');
            let url = Backbone.history.generateUrl('editContent', {
                contentTypeId: content.get('content_type'),
                contentId: content.get('content_id'),
                language: user.language.contribution
            });
            Backbone.history.navigate(url, true);
            this.hide();
        });
    }
}

export default ContentRestoreModalView;
