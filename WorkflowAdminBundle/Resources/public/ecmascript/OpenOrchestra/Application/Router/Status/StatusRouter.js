import OrchestraRouter from '../OrchestraRouter'
import Application     from '../../Application'
import UserFormView    from '../../View/User/UserFormView'
import FormBuilder     from '../../../Service/Form/Model/FormBuilder'
import Statuses        from '../../Collection/Status/Statuses'
import StatusesView    from '../../View/Status/StatusesView'

/**
 * @class StatusRouter
 */
class StatusRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'status/edit/:statusId': 'editStatus',
            'status/list(/:page)': 'listStatus'
        };
    }

    /**
     * Edit Status
     *
     * @param  {String} statusId
     */
    editStatus(statusId) {
        let url = Routing.generate('open_orchestra_workflow_admin_status_form', {statusId: statusId});
        this._diplayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let statusFormView = new StatusFormView({form: form, statusId: statusId});
            Application.getRegion('content').html(statusFormView.render().$el);
        });
    }

    /**
     *  List Status
     *
     * @param {String} page
     */
    listStatus(page) {
        if (null === page) {
            page = 1
        }
        this._diplayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new Statuses().fetch({
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (statuses) => {
                let statusesView = new StatusesView({
                    collection: statuses,
                    settings: {
                        page: page,
                        deferLoading: [statuses.recordsTotal, statuses.recordsFiltered],
                        data: statuses.models,
                        pageLength: pageLength
                    }
                });
                let el = statusesView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }
}

export default StatusRouter;
