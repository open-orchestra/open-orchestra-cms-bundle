import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import ContentSummaryView from '../../View/Content/ContentSummaryView'
import ContentsView       from '../../View/Content/ContentsView'
import ContentTypes       from '../../Collection/ContentType/ContentTypes'
import Contents           from '../../Collection/Content/Contents'
import ContentType        from '../../Model/ContentType/ContentType'
import Statuses           from '../../Collection/Status/Statuses'

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
            'content/summary': 'showContentSummary',
            'content/list/:contentTypeId/:language/:contentTypeName(/:page)': 'listContent'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.platform.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.platform.content'),
                link: '#'+Backbone.history.generateUrl('showContentSummary')
            }
        ]
    }

    /**
     * show content summary
     */
    showContentSummary() {
        this._displayLoader(Application.getRegion('content'));
        let contentTypes = new ContentTypes();

        contentTypes.fetch({
            context: 'list_content_type_for_content',
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
     * list content by content type
     */
    listContent(contentTypeId, language, contentTypeName, page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        let urlParameter = {
            contentTypeId: contentTypeId,
            siteId: Application.getContext().siteId,
            language: language,
            contentTypeName: contentTypeName,
        };
        
        let contentType = new ContentType();
        let statuses = new Statuses();
        let contents = new Contents();
        
        $.when(
            statuses.fetch({context: 'contents'}),
            contentType.fetch({urlParameter: {contentTypeId: contentTypeId}}),
            contents.fetch({
                context: 'list',
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
