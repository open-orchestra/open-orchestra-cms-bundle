import OrchestraRouter  from '../OrchestraRouter'
import Application      from '../../Application'
import Blocks           from '../../Collection/Block/Blocks'
import SharedBlocksView from '../../View/Block/SharedBlocksView'
import FormBuilder      from '../../../Service/Form/Model/FormBuilder'
import BlockFormView    from '../../View/Block/BlockFormView'
import NewBlockFormView from '../../View/Block/NewBlockFormView'
import NewBlockListView from '../../View/Block/NewBlockListView'
import BlockComponents  from '../../Collection/Block/BlockComponents'

/**
 * @class BlockRouter
 */
class BlockRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'shared-block/list(/:language)(/:page)': 'listSharedBlock',
            'block/new/list/:language': 'newBlockList',
            'block/new/:component/:language/:name': 'newBlock',
            'block/edit/:blockId/:blockLabel': 'editBlock'
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
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.shared_block'),
                link: '#'+Backbone.history.generateUrl('listSharedBlock')
            }
        ]
    }

    /**
     * List shared block
     *
     * @param {string} language
     * @param {int}    page
     */
    listSharedBlock(language, page) {
        if (null === language) {
            language = Application.getContext().user.language.contribution
        }
        if (null === page) {
            page = 1
        }
        this._diplayLoader(Application.getRegion('content'));
        page = Number(page) - 1;
        new Blocks().fetch({
            urlParameter: {
                language: language
            },
            success: (blocks) => {
                let sharedBlocksView = new SharedBlocksView({
                    collection: blocks,
                    language: language,
                    siteLanguages: Application.getContext().siteLanguages,
                    settings: {
                        page: page,
                        serverSide: false,
                        data: blocks.models
                    }
                });
                let el = sharedBlocksView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }

    /**
     * New block list component
     *
     * @param {string} language
     */
    newBlockList(language) {
        this._diplayLoader(Application.getRegion('content'));
        new BlockComponents().fetch({
            success: (blockComponents) => {
                let newBlockListView = new NewBlockListView({
                    blockComponents : blockComponents,
                    language: language
                });
                Application.getRegion('content').html(newBlockListView.render().$el);
            }
        })
    }

    /**
     * New block
     *
     * @param {string} component
     * @param {string} language
     * @param {string} name
     */
    newBlock(component, language, name) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_shared_block_new', {
            component : component,
            language : language
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let newBlockFormView = new NewBlockFormView({
                form : form,
                language : language,
                name: name
            });
            Application.getRegion('content').html(newBlockFormView.render().$el);
        });
    }

    /**
     * Edit block
     *
     * @param {string} blockId
     * @param {string} blockLabel
     */
    editBlock(blockId, blockLabel) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_block_form', {
            blockId : blockId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let blockFormView = new BlockFormView({
                form : form,
                blockLabel: blockLabel,
                blockId: blockId
            });
            Application.getRegion('content').html(blockFormView.render().$el);
        });
    }
}

export default BlockRouter;
