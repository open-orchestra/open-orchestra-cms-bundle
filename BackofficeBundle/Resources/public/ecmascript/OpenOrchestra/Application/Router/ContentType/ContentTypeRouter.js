import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import FormBuilder        from '../../../Service/Form/Model/FormBuilder'
import ContentTypeFormView from '../../View/ContentType/ContentTypeFormView'

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
            'contentType/edit/:contentTypeId/:name': 'editContentType'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.content_type'),
                link: '#'
            }
        ]
    }

    /**
     * Edit contentType
     *
     * @param {string} contentTypeId
     */
    editContentType(contentTypeId, name) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_type_form', {
            contentTypeId : contentTypeId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let contentTypeFormView = new ContentTypeFormView({
                form: form,
                name: name
            });
            Application.getRegion('content').html(contentTypeFormView.render().$el);
        });
    }
}

export default ContentTypeRouter;
