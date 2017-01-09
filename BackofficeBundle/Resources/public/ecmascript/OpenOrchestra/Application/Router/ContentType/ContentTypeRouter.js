import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import FormBuilder        from '../../../Service/Form/Model/FormBuilder'
import ContentTypeFormView from '../../View/ContentType/ContentTypeFormView'
import ContentTypes     from '../../Collection/ContentType/ContentTypes'
import ContentTypesView from '../../View/ContentType/ContentTypesView'


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
            'contentType/edit/:contentTypeId/:name': 'editContentType',
            'content-type/list(/:page)': 'listContentType'
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
     * @param {string} name
     */
    editContentType(contentTypeId, name) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_type_form', {
            contentTypeId: contentTypeId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let contentTypeFormView = new ContentTypeFormView({
                form: form,
                name: name
            });
            Application.getRegion('content').html(contentTypeFormView.render().$el);
        });
    }

    /**
     * List content type
     *
     * @param {int} page
     */
    listContentType(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let collection = new ContentTypes();
        let contentTypesView = new ContentTypesView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = contentTypesView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default ContentTypeRouter;
