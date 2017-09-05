import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import CellFormatterManager        from 'OpenOrchestra/Service/DataFormatter/Manager'
import SiteFormatter               from 'OpenOrchestra/Service/DataFormatter/SiteFormatter'

/**
 * @class RedirectionsListView
 */
class RedirectionsListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'redirection_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "site_id",
                title: Translator.trans('open_orchestra_backoffice.table.redirections.site_name'),
                orderable: true,
                visibile: true,
                createdCell: CellFormatterManager.format({type: 'site'})
            },
            {
                name: "id",
                visible: false
            },
            {
                name: "route_pattern",
                title: Translator.trans('open_orchestra_backoffice.table.redirections.route_pattern'),
                orderable: true,
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "locale",
                title: Translator.trans('open_orchestra_backoffice.table.redirections.locale'),
                orderable: true,
                visibile: true
            },
            {
                name: "redirection",
                title: Translator.trans('open_orchestra_backoffice.table.redirections.redirection'),
                orderable: false,
                visibile: true
            },
            {
                name: "permanent",
                title: Translator.trans('open_orchestra_backoffice.table.redirections.permanent'),
                orderable: true,
                visibile: true,
                render: this._translateLinkedToSite
            }
        ];
    }

    /**
     * @param {Object|string} data
     * @private
     */
    _translateLinkedToSite(data) {
        return Translator.trans('open_orchestra_backoffice.table.' + data);
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listRedirections', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editRedirection', {
            redirectionId: rowData.get('id'),
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * Return options used to fetch collection
     *
     * @returns {{}}
     * @private
     */
    _getSyncOptions() {
        return {apiContext: 'list'};
    }
}

export default RedirectionsListView;
