import OrchestraRouter    from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application        from 'OpenOrchestra/Application/Application'

import TrashItemsView     from 'OpenOrchestra/Application/View/TrashItem/TrashItemsView'

import TrashItems         from 'OpenOrchestra/Application/Collection/TrashItem/TrashItems'

/**
 * @class TrashItemRouter
 */
class TrashItemRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'trashitem/list(/:page)': 'listTrashItem'
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
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.trashcan'),
                link: '#'+Backbone.history.generateUrl('listTrashItem')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-trashcan'
        };
    }

    /**
     * List trash item
     *
     * @param {int} page
     */
    listTrashItem(page = 1) {
        if (null === page) {
            page = 1
        }
        page = Number(page) - 1;
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        this._displayLoader(Application.getRegion('content'));
        let collection = new TrashItems();
        let listView = new TrashItemsView({
            collection: collection,
            settings: {
                page: page,
                pageLength: pageLength
            }
        });
        let el = listView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default TrashItemRouter;
