import AbstractBlockRouter       from 'OpenOrchestra/Application/Router/Block/AbstractBlockRouter'
import Application               from 'OpenOrchestra/Application/Application'
import FormBuilder               from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import Blocks                    from 'OpenOrchestra/Application/Collection/Block/Blocks'

import NewBlockComponentListView from 'OpenOrchestra/Application/View/Block/NewBlockComponentListView'
import BlockFormView             from 'OpenOrchestra/Application/View/Block/BlockFormView'
import NewBlockListAvailableView from 'OpenOrchestra/Application/View/Block/NewBlockListAvailableView'
import NewBlockFormView          from 'OpenOrchestra/Application/View/Block/NewBlockFormView'

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
            'block/edit/:blockId/:nodeId/:nodeLanguage/:nodeVersion': 'editBlock',
            'block/read/:blockId/:nodeId/:nodeLanguage/:nodeVersion': 'readBlock',
            'block/new/form/:nodeId/:nodeLanguage/:nodeVersion/:component/:componentName/:areaName/:position': 'newBlockForm'
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
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.node'),
                link: '#'+Backbone.history.generateUrl('showNodes')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-node'
        };
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
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    editBlock(blockId, nodeId, nodeLanguage, nodeVersion) {
        let url = Routing.generate('open_orchestra_backoffice_block_form', {
            blockId : blockId
        });
        this._createForm(url, blockId, nodeId, nodeLanguage, nodeVersion);
    }

    /**
     * Read block
     *
     * @param {string} blockId
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    readBlock(blockId, nodeId, nodeLanguage, nodeVersion) {
        let url = Routing.generate('open_orchestra_backoffice_block_read', {
            blockId : blockId
        });
        this._createForm(url, blockId, nodeId, nodeLanguage, nodeVersion);
    }

    /**
     * create block form
     *
     * @param {string} url
     * @param {string} blockId
     * @param {string} nodeId
     * @param {string} nodeLanguage
     * @param {string} nodeVersion
     */
    _createForm(url, blockId, nodeId, nodeLanguage, nodeVersion) {
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let blockFormView = new BlockFormView({
                form : form,
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
