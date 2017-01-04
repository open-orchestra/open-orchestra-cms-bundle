import OrchestraRouter  from '../OrchestraRouter'
import Application      from '../../Application'
import Blocks           from '../../Collection/Blocks/Blocks'
import SharedBlocksView from '../../View/Block/SharedBlocksView'
import FormBuilder      from '../../../Service/Form/Model/FormBuilder'
import BlockFormView     from '../../View/Block/BlockFormView'

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
            'block/edit/:blockId': 'editBlock'
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
     * Edit block
     *
     * @param {string} blockId
     */
    editBlock(blockId) {
        this._diplayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_block_form', {
            blockId : blockId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let blockFormView = new BlockFormView({
                form : form
            });
            Application.getRegion('content').html(blockFormView.render().$el);
        });
    }
}

export default BlockRouter;
