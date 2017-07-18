import AbstractDataTableView from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class LogListView
 */
class LogListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'log_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "date_time",
                title: Translator.trans('open_orchestra_log.table.date'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true
            },
            {
                name: "user_ip",
                title: Translator.trans('open_orchestra_log.table.user_ip'),
                orderable: true,
                visibile: true
            },
            {
                name: "user_name",
                title: Translator.trans('open_orchestra_log.table.user_name'),
                orderable: true,
                visibile: true
            },
            {
                name: "message",
                title: Translator.trans('open_orchestra_log.table.message'),
                orderable: true,
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listLog', {page : page});
    }
}

export default LogListView;
