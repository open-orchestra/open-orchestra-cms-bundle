import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Logs            from '../../Collection/Log/Logs'
import LogsView        from '../../View/Log/LogsView'

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
                label: Translator.trans('open_orchestra_backoffice.navigation.configuration.title')
            },
            {
                label: Translator.trans('open_orchestra_log.navigation.configuration.logs'),
                link: '#' + Backbone.history.generateUrl('listLog')
            }
        ]
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
        let collection = new Logs();
        let logsView = new LogsView({
            collection: collection,
            settings: {page: Number(page) - 1}
        });
        let el = logsView.render().$el;
        Application.getRegion('content').html(el);
    }
}

export default LogRouter;
