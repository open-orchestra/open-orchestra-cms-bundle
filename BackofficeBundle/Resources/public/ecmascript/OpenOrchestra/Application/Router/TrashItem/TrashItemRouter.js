import OrchestraRouter    from '../OrchestraRouter'
import Application        from '../../Application'

import TrashItemsView     from '../../View/TrashItem/TrashItemsView'

import TrashItems         from '../../Collection/TrashItem/TrashItems'

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
                label: Translator.trans('open_orchestra_backoffice.navigation.contribution.title')
            },
            {
                label: Translator.trans('open_orchestra_backoffice.navigation.contribution.trashcan'),
                link: '#'+Backbone.history.generateUrl('listTrashItem')
            }
        ]
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
        this._displayLoader(Application.getRegion('content'));
        let collection = new TrashItems();
        let listView = new TrashItemsView({
            collection: collection,
            settings: {
                page: page
            }
        });
        let el = listView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default TrashItemRouter;
