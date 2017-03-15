import AbstractWorkflowRouter from '../AbstractWorkflowRouter'
import Application            from '../../Application'
import StatusFormView         from '../../View/Status/StatusFormView'
import FormBuilder            from '../../../Service/Form/Model/FormBuilder'
import Statuses               from '../../Collection/Status/Statuses'
import StatusesView           from '../../View/Status/StatusesView'

/**
 * @class StatusRouter
 */
class StatusRouter extends AbstractWorkflowRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'workflow/status/new'           : 'newStatus',
            'workflow/status/edit/:statusId': 'editStatus',
            'workflow/status/list(/:page)'  : 'listStatus'
        };
    }

    /**
     * @inheritdoc
     */
    getNavigationHighlight() {
        return {
            newStatus  : 'course-workflow',
            editStatus : 'course-workflow',
            listStatus : 'course-workflow'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumbHighlight() {
        return {
            newStatus  : 'course-status',
            editStatus : 'course-status',
            listStatus : 'course-status'
        };
    }

    /**
     * New status
     */
    newStatus() {
        let url = Routing.generate('open_orchestra_workflow_admin_status_new');

        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let statusFormView = new StatusFormView({
                form: form
             });
            Application.getRegion('content').html(statusFormView.render().$el);
        });
    }

    /**
     * Edit Status
     *
     * @param  {String} statusId
     */
    editStatus(statusId) {
        let url = Routing.generate('open_orchestra_workflow_admin_status_form', {statusId: statusId});
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let statusFormView = new StatusFormView({
                form: form,
                statusId: statusId
            });
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
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new Statuses().fetch({
            apiContext: 'table',
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
