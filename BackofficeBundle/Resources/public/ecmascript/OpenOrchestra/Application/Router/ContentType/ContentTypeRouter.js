import OrchestraRouter     from '../OrchestraRouter'
import Application         from '../../Application'
import ContentTypeFormView from '../../View/ContentType/ContentTypeFormView'
import FormBuilder         from '../../../Service/Form/Model/FormBuilder'

/**
 * @class ContentTypeRouter
 */
class ContentTypeRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'contentType/edit/:contentTypeId': 'editContentType',
        };
    }

    /**
     * Edit ContentType
     */
    editContentType(contentTypeId) {
        let url = Routing.generate('open_orchestra_backoffice_content_type_form', {contentTypeId : contentTypeId});
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let contentTypeFormView = new ContentTypeFormView({form : form, contentTypeId: contentTypeId});
            Application.getRegion('content').html(contentTypeFormView.render().$el);
        });

        return false;
    }
}

export default ContentTypeRouter;
