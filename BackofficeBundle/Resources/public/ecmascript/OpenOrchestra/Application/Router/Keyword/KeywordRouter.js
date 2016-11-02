import OrchestraRouter from '../OrchestraRouter'
import app             from '../../Application'
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
    preinitialize(options) {
        this.routes = {
            'keyword/list(/:page)': 'listKeyword',
            'keyword/test/:test(/:page)': 'test'
        };
    }

    /**
     * List Keyword
     *
     * @param {int} page
     */
    listKeyword(page = 1) {
        this._diplayLoader(app.getRegion('content'));
        let collection = new Keywords();
        let keywordView = new KeywordListView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = keywordView.render().$el;
        app.getRegion('content').html(el);
    }
}

export default KeywordRouter;
