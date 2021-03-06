import OrchestraRouter from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application     from 'OpenOrchestra/Application/Application'
import Logs            from 'OpenOrchestra/Application/Collection/Log/Logs'
import LogsView        from 'OpenOrchestra/Application/View/Log/LogsView'

/**
 * @class LogRouter
 */
class LogRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'log/list(/:page)': 'listLog'
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
                label: Translator.trans('open_orchestra_log.menu.configuration.log'),
                link: '#' + Backbone.history.generateUrl('listLog')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-log'
        };
    }

    /**
     * List Logs
     *
     * @param {int} page
     */
    listLog(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        let collection = new Logs();
        let logsView = new LogsView({
            collection: collection,
            settings: {
                page: Number(page) - 1,
                pageLength: pageLength
            }
        });
        let el = logsView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default LogRouter;
