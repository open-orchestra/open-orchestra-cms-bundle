import OrchestraRouter     from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application         from 'OpenOrchestra/Application/Application'
import FormBuilder         from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import ContentTypeFormView from 'OpenOrchestra/Application/View/ContentType/ContentTypeFormView'
import ContentTypes        from 'OpenOrchestra/Application/Collection/ContentType/ContentTypes'
import ContentTypesView    from 'OpenOrchestra/Application/View/ContentType/ContentTypesView'


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
            'content-type/list(/:page)'       : 'listContentType',
            'content-type/edit/:contentTypeId': 'editContentType',
            'content-type/new'                : 'newContentType'
        };
    }

    /**

     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.developer.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.developer.content_type'),
                link: '#' + Backbone.history.generateUrl('listContentType')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-content-type'
        };
    }

    /**
     * Edit contentType
     *
     * @param {string} contentTypeId
     */
    editContentType(contentTypeId) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_type_form', {
            contentTypeId: contentTypeId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let contentTypeFormView = new ContentTypeFormView({
                form: form,
                contentTypeId: contentTypeId
            });
            Application.getRegion('content').html(contentTypeFormView.render().$el);
        });
    }

    /**
     * Create contentType
     */
    newContentType() {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_type_new');
        FormBuilder.createFormFromUrl(url, (form) => {
            let contentTypeFormView = new ContentTypeFormView({
                form: form
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
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        page = Number(page) - 1;
        new ContentTypes().fetch({
            apiContext: 'list',
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (contentTypes) => {
                let contentTypesView = new ContentTypesView({
                    collection: contentTypes,
                    settings: {
                        page: page,
                        deferLoading: [contentTypes.recordsTotal, contentTypes.recordsFiltered],
                        data: contentTypes.models,
                        pageLength: pageLength
                    }
                });
                let el = contentTypesView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }
}

export default ContentTypeRouter;
