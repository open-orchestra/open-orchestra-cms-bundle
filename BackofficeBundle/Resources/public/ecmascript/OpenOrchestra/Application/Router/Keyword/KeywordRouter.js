import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'

import KeywordsView    from '../../View/Keyword/KeywordsView'
import KeywordFormView from '../../View/Keyword/KeywordFormView'

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
            'keyword/new'            : 'newKeyword',
            'keyword/edit/:keywordId': 'editKeyword',
            'keyword/list(/:page)'   : 'listKeyword'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.platform.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.platform.tag'),
                link: '#'+Backbone.history.generateUrl('listKeyword')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-keyword'
        };
    }

    /**
     * New Keyword
     */
    newKeyword() {
        let url = Routing.generate('open_orchestra_backoffice_keyword_new');
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let keywordFormView = new KeywordFormView({
                form: form
            });
            Application.getRegion('content').html(keywordFormView.render().$el);
        });
    }

    /**
     * Edit Keyword
     *
     * @param {String} keywordId
     */
    editKeyword(keywordId) {
        let url = Routing.generate('open_orchestra_backoffice_keyword_form', {keywordId: keywordId});
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let keywordFormView = new KeywordFormView({
                form: form,
                keywordId: keywordId
            });
            Application.getRegion('content').html(keywordFormView.render().$el);
        });
    }

    /**
     * List Keyword
     *
     * @param {int} page
     */
    listKeyword(page = 1) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new Keywords().fetch({
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (keywords) => {
                let keywordView = new KeywordsView({
                    collection: keywords,
                    settings: {
                        page: page,
                        deferLoading: [keywords.recordsTotal, keywords.recordsFiltered],
                        data: keywords.models,
                        pageLength: pageLength
                    }
                });
                let el = keywordView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }
}

export default KeywordRouter;
