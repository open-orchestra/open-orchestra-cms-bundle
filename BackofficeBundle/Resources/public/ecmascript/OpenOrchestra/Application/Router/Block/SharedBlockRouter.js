import AbstractBlockRouter             from 'OpenOrchestra/Application/Router/Block/AbstractBlockRouter'

import Application                     from 'OpenOrchestra/Application/Application'
import Blocks                          from 'OpenOrchestra/Application/Collection/Block/Blocks'
import FormBuilder                     from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import BlockComponents                 from 'OpenOrchestra/Application/Collection/Block/BlockComponents'

import SharedBlockFormView             from 'OpenOrchestra/Application/View/Block/SharedBlockFormView'
import NewSharedBlockFormView          from 'OpenOrchestra/Application/View/Block/NewSharedBlockFormView'
import NewSharedBlockComponentListView from 'OpenOrchestra/Application/View/Block/NewSharedBlockComponentListView'
import SharedBlocksView                from 'OpenOrchestra/Application/View/Block/SharedBlocksView'

/**
 * @class SharedBlockRouter
 */
class SharedBlockRouter extends AbstractBlockRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'shared-block/list(/:language)(/:page)'                   : 'listSharedBlock',
            'shared-block/edit/:blockId/:language(/:activateUsageTab)': 'editSharedBlock',
            'shared-block/new/list/:language'                         : 'newSharedBlockListComponent',
            'shared-block/new/:component/:language'                   : 'newSharedBlock'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.menu.configuration.shared_block'),
                link: '#'+Backbone.history.generateUrl('listSharedBlock')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-shared-block'
        };
    }

    /**
     * List shared block
     *
     * @param {string} language
     * @param {int}    page
     */
    listSharedBlock(language, page) {
        if (null === language) {
            let user = Application.getContext().get('user');
            language = user.language.contribution;
        }
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        page = Number(page) - 1;
        let blocks = new Blocks();
        let blockComponents = new BlockComponents();
        $.when(
            blocks.fetch({
                urlParameter: { language: language },
                apiContext: 'list-table-shared-block',
                data : {
                    start: page * pageLength,
                    length: pageLength
                }
            }),
            blockComponents.fetch()
        ).done(() => {
            let sharedBlocksView = new SharedBlocksView({
                collection: blocks,
                blockComponents: blockComponents,
                language: language,
                siteLanguages: Application.getContext().get('siteLanguages'),
                settings: {
                    page: page,
                    deferLoading: [blocks.recordsTotal, blocks.recordsFiltered],
                    data: blocks.models,
                    pageLength: pageLength
                }
            });
            let el = sharedBlocksView.render().$el;
            Application.getRegion('content').html(el);
        });
    }

    /**
     * New shared block list component
     *
     * @param {string} language
     */
    newSharedBlockListComponent(language) {
        this._newBlockListComponent(NewSharedBlockComponentListView, language);
    }

    /**
     * New block
     *
     * @param {string} component
     * @param {string} language
     * @param {string} name
     */
    newSharedBlock(component, language, name) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_shared_block_new', {
            component : component,
            language : language
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let newSharedBlockFormView = new NewSharedBlockFormView({
                form : form,
                language : language,
                name: name
            });
            Application.getRegion('content').html(newSharedBlockFormView.render().$el);
        });
    }

    /**
     * Edit shared block
     *
     * @param {string} blockId
     * @param {string} language
     * @param {boolean} activateUsageTab
     */
    editSharedBlock(blockId, language, activateUsageTab) {
        if (null === activateUsageTab) {
            activateUsageTab = false;
        }
        activateUsageTab = (activateUsageTab === 'true');
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_block_form', {
            blockId : blockId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let sharedblockFormView = new SharedBlockFormView({
                form : form,
                blockId: blockId,
                language: language,
                activateUsageTab: activateUsageTab
            });
            Application.getRegion('content').html(sharedblockFormView.render().$el);
        });
    }
}

export default SharedBlockRouter;
