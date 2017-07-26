import OrchestraRouter                 from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application                     from 'OpenOrchestra/Application/Application'
import BlockComponents                 from 'OpenOrchestra/Application/Collection/Block/BlockComponents'

/**
 * @class AbstractBlockRouter
 */
class AbstractBlockRouter extends OrchestraRouter
{
    /**
     * New block list component
     *
     * @param {String}                            language
     * @param {Object}                            viewParameters
     * @param {AbstractNewBlockComponentListView} newBlockComponentListView
     */
    _newBlockListComponent(newBlockComponentListView, language, viewParameters = {}) {
        this._displayLoader(Application.getRegion('content'));
        new BlockComponents().fetch({
            success: (blockComponents) => {
                viewParameters = $.extend({}, viewParameters, {
                    blockComponents : blockComponents,
                    language: language
                });
                let newBlockListView = new newBlockComponentListView(viewParameters);
                Application.getRegion('content').html(newBlockListView.render().$el);
            }
        })
    }
}

export default AbstractBlockRouter;
