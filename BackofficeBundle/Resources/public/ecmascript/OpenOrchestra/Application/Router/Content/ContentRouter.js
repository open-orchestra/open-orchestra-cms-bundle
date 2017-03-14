import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import FormBuilder        from '../../../Service/Form/Model/FormBuilder'
import ContentSummaryView from '../../View/Content/ContentSummaryView'
import ContentsView       from '../../View/Content/ContentsView'
import ContentFormView    from '../../View/Content/ContentFormView'
import NewContentFormView from '../../View/Content/NewContentFormView'
import ContentTypes       from '../../Collection/ContentType/ContentTypes'
import Contents           from '../../Collection/Content/Contents'
import ContentType        from '../../Model/ContentType/ContentType'
import Content            from '../../Model/Content/Content'
import Statuses           from '../../Collection/Status/Statuses'
import ApplicationError   from '../../../Service/Error/ApplicationError'
import ConfirmModalView from '../../../Service/ConfirmModal/View/ConfirmModalView'

/**
 * @class ContentRouter
 */
class ContentRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'content/summary'                                             : 'showContentSummary',
            'content/list/:contentTypeId/:language(/:page)'               : 'listContent',
            'content/edit/:contentTypeId/:language/:contentId(/:version)' : 'editContent',
            'content/new/:contentTypeId/:language'                        : 'newContent'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.content'),
                link: '#'+Backbone.history.generateUrl('showContentSummary')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-content'
        };
    }

    /**
     * show content summary
     */
    showContentSummary() {
        this._displayLoader(Application.getRegion('content'));
        let contentTypes = new ContentTypes();

        contentTypes.fetch({
            apiContext: 'list_content_type_for_content',
            success: () => {
                let contentSummaryView = new ContentSummaryView({
                    contentTypes: contentTypes
                });
                let el = contentSummaryView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }


    /**
     * Edit content
     *
     * @param {string}   contentTypeId
     * @param {string}   language
     * @param {string}   contentId
     * @param {int|null} version
     */
    editContent(contentTypeId, language, contentId, version = null) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_form', {
            contentId: contentId,
            language: language,
            version: version
        });

        let siteLanguageUrl = [];
        for (let siteLanguage of Application.getContext().siteLanguages) {
            siteLanguageUrl[siteLanguage] = Backbone.history.generateUrl('editContent', {
                contentTypeId: contentTypeId,
                language: siteLanguage,
                contentId: contentId
            });
        }

        new ContentType().fetch({
            urlParameter: {contentTypeId: contentTypeId},
            success: (contentType) => {
                FormBuilder.createFormFromUrl(url, (form, jqXHR) => {
                    let version = jqXHR.getResponseHeader('version');
                    if (null === version) {
                        throw new ApplicationError('Invalid version');
                    }
                    let contentFormView = new ContentFormView({
                        form: form,
                        contentType: contentType,
                        language: language,
                        siteLanguageUrl: siteLanguageUrl,
                        contentId: contentId,
                        version: version
                    });
                    Application.getRegion('content').html(contentFormView.render().$el);
                },
                null, null, () => {
                    let noCallback = () => {
                        let url = Backbone.history.generateUrl('listContent',{
                            contentTypeId: contentTypeId,
                            language: language
                        });
                        Backbone.history.navigate(url, true);
                    };
                    let yesCallback = () => {
                        new Content().save({}, {
                            apiContext: 'new-language',
                            urlParameter: {
                                contentId: contentId,
                                language: language,
                            },
                            success: () => {
                                Backbone.history.loadUrl(Backbone.history.fragment);
                            }
                        })
                    };

                    let confirmModalView = new ConfirmModalView({
                        confirmTitle: Translator.trans('open_orchestra_backoffice.content.confirm_create.title'),
                        confirmMessage: Translator.trans('open_orchestra_backoffice.content.confirm_create.message'),
                        yesCallback: yesCallback,
                        context: this,
                        noCallback: noCallback
                    });

                    Application.getRegion('modal').html(confirmModalView.render().$el);
                    confirmModalView.show();
                });
            }
        });
    }

    /**
     * Create contentType
     *
     * @param {string} contentTypeId
     * @param {string} language
     */
    newContent(contentTypeId, language) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_content_new', {
            contentTypeId: contentTypeId,
            language: language
        });
        let siteLanguageUrl = [];
        for (let siteLanguage of Application.getContext().siteLanguages) {
            siteLanguageUrl[siteLanguage] = Backbone.history.generateUrl('newContent', {contentTypeId: contentTypeId, language: siteLanguage});
        }

        FormBuilder.createFormFromUrl(url, (form) => {
            let newContentFormView = new NewContentFormView({
                form: form,
                contentTypeId: contentTypeId,
                language: language,
                siteLanguageUrl: siteLanguageUrl
            });
            Application.getRegion('content').html(newContentFormView.render().$el);
        });
    }

    /**
     * list content by content type
     */
    listContent(contentTypeId, language, page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        let urlParameter = {
            contentTypeId: contentTypeId,
            siteId: Application.getContext().siteId,
            language: language
        };
        
        let contentType = new ContentType();
        let statuses = new Statuses();
        let contents = new Contents();
        
        $.when(
            statuses.fetch({apiContext: 'contents'}),
            contentType.fetch({urlParameter: {contentTypeId: contentTypeId}}),
            contents.fetch({
                apiContext: 'list',
                urlParameter: urlParameter,
                data : {
                    start: page * pageLength,
                    length: pageLength
                }
            })
        ).done( () => {
            let contentsView = new ContentsView({
                collection: contents,
                settings: {
                    page: page,
                    deferLoading: [contents.recordsTotal, contents.recordsFiltered],
                    data: contents.models,
                    pageLength: pageLength
                },
                urlParameter: urlParameter,
                contentType: contentType,
                statuses: statuses
            });
            let el = contentsView.render().$el;
            Application.getRegion('content').html(el);
         });
    }
}

export default ContentRouter;
