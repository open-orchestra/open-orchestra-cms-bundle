import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'
import ContentSummaryView from '../../View/Content/ContentSummaryView'
import ContentsView       from '../../View/Content/ContentsView'
import ContentTypes       from '../../Collection/ContentType/ContentTypes'
import Contents           from '../../Collection/Content/Contents'
import ContentType        from '../../Model/ContentType/ContentType'

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
        new ContentType().fetch({
            urlParameter: {
                contentTypeId: contentTypeId
            },
            success: (contentType) => {
                let collection = new Contents();
                let contentsView = new ContentsView({
                    collection: collection,
                    settings: {
                        page: Number(page) - 1,
                    },
                    urlParameter: {
                        contentTypeId: contentTypeId,
                        siteId: Application.getContext().siteId,
                        language: language,
                        contentTypeName: contentTypeName,
                    },
                    contentType: contentType
                });
                let el = contentsView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }
}

export default ContentRouter;
