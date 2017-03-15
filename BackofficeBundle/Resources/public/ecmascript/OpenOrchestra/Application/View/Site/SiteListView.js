import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'

/**
 * @class SiteListView
 */
class SiteListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin)
{

    /**
     * @param {Object}  collection
     * @param {Array}   settings
     * @param {boolean} inPlatformContext
     */
    initialize({collection, settings, inPlatformContext}) {
        super.initialize({collection, settings});
        this._inPlatformContext = inPlatformContext;
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
        return Backbone.history.generateUrl(this._inPlatformContext ? 'listPlatformSite' : 'listSite', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl(this._inPlatformContext ? 'editPlatformSite' : 'editSite', {
            siteId: rowData.get('site_id'),
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default SiteListView;
