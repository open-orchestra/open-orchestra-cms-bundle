import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import KeywordListView from '../../View/Keyword/KeywordListView'
import Keywords        from '../../Collection/Keyword/Keywords'

/**
 * @class KeywordRouter
 */
class KeywordRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'keyword/list(/:page)': 'listKeyword'
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
                label: Translator.trans('open_orchestra_backoffice.navigation.platform.tag'),
                link: '#'+Backbone.history.generateUrl('listKeyword')
            }
        ]
    }

    /**
     * List Keyword
     *
     * @param {int} page
     */
    listKeyword(page = 1) {
        this._displayLoader(Application.getRegion('content'));
        let collection = new Keywords();
        let keywordView = new KeywordListView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = keywordView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default KeywordRouter;
