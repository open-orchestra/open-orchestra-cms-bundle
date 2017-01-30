import AbstractBlockRouter       from './AbstractBlockRouter'
import Application               from '../../Application'
import FormBuilder               from '../../../Service/Form/Model/FormBuilder'
import BlockComponents           from '../../Collection/Block/BlockComponents'
import Blocks                    from '../../Collection/Block/Blocks'

import NewBlockComponentListView from '../../View/Block/NewBlockComponentListView'
import BlockFormView             from '../../View/Block/BlockFormView'
import NewBlockListAvailableView from '../../View/Block/NewBlockListAvailableView'
import NewBlockFormView          from '../../View/Block/NewBlockFormView'

/**
 * @class BlockRouter
 */
class BlockRouter extends AbstractBlockRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.routes = {
            'block/new/list/:nodeId/:nodeLanguage/:nodeVersion/:areaName/:position': 'newBlockListComponent',
            'block/new/list/available-component/:nodeId/:nodeLanguage/:nodeVersion/:component/:componentName/:areaName/:position': 'newBlockListAvailable',
            'block/edit/:blockId/:blockLabel/:nodeId/:nodeLanguage/:nodeVersion': 'editBlock',
            'block/new/form/:nodeId/:nodeLanguage/:nodeVersion/:component/:componentName/:areaName/:position': 'newBlockForm'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.contribution.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.contribution.nodes'),
                link: '#'+Backbone.history.generateUrl('showNodes')
            }
        ]
    }

    /**
     * New block list component
     *
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     * @param {string} areaName
     * @param {string} position
     */
    newBlockListComponent(nodeId, nodeLanguage, nodeVersion, areaName, position) {
        this._newBlockListComponent(NewBlockComponentListView, nodeLanguage, {
            nodeId: nodeId,
            nodeVersion: nodeVersion,
            areaName: areaName,
            position: position
        });
    }

    /**
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     * @param {string} component
     * @param {string} componentName
     * @param {string} areaName
     * @param {string} position
     */
    newBlockListAvailable(nodeId, nodeLanguage, nodeVersion, component, componentName, areaName, position) {
        this._displayLoader(Application.getRegion('content'));
        new Blocks().fetch({
            urlParameter: {
                language: nodeLanguage,
                component: component
            },
            apiContext: 'list-by-component-shared-block',
            success: (blocks) => {
                let blockFormView = new NewBlockListAvailableView({
                    blocks: blocks,
                    nodeId: nodeId,
                    nodeLanguage: nodeLanguage,
                    nodeVersion: nodeVersion,
                    component: component,
                    componentName: componentName,
                    areaName: areaName,
                    position: position
                });
                Application.getRegion('content').html(blockFormView.render().$el);
            }
        });
    }

    /**
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     * @param {string} component
     * @param {string} componentName
     * @param {string} areaName
     * @param {string} position
     */
    newBlockForm(nodeId, nodeLanguage, nodeVersion, component, componentName, areaName, position) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_block_new_in_node', {
            nodeId: nodeId,
            language: nodeLanguage,
            version: nodeVersion,
            component: component,
            areaId: areaName,
            position: position
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let newBlockFormView = new NewBlockFormView({
                form : form,
                name: componentName,
                nodeId: nodeId,
                nodeLanguage: nodeLanguage,
                nodeVersion: nodeVersion,
                component: component,
                areaName: areaName,
                position: position
            });
            Application.getRegion('content').html(newBlockFormView.render().$el);
        });
    }

    /**
     * Edit block
     *
     * @param {string} blockId
     * @param {string} blockLabel
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    editBlock(blockId, blockLabel, nodeId, nodeLanguage, nodeVersion) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('open_orchestra_backoffice_block_form', {
            blockId : blockId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let blockFormView = new BlockFormView({
                form : form,
                blockLabel: blockLabel,
                blockId: blockId,
                nodeId: nodeId,
                nodeLanguage: nodeLanguage,
                nodeVersion: nodeVersion
            });
            Application.getRegion('content').html(blockFormView.render().$el);
        });
    }
}

export default BlockRouter;
