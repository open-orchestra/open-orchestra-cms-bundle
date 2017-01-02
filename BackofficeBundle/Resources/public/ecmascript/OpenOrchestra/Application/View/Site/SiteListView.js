import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class SiteListView
 */
class SiteListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change .delete-checkbox'] = '_changeDeleteCheckbox';
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'site_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "delete",
                orderable: false,
                width: '20px',
                createdCell: this._createCheckbox
            },
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.sites.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "site_id",
                title: Translator.trans('open_orchestra_backoffice.table.sites.site_id'),
                orderable: false,
                visibile: true
            }
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listSite', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editSite', {
            siteId: rowData.get('site_id')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let $checkbox = $('<input>', {type: 'checkbox', id: id, class:'delete-checkbox'});
        $checkbox.data(rowData);
        $(td).append($checkbox);
        $(td).append($('<label>', {for: id}))
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _changeDeleteCheckbox(event) {
        let site = $(event.currentTarget).data();
        site.set('delete', $(event.currentTarget).prop('checked'));
    }
}

export default SiteListView;
