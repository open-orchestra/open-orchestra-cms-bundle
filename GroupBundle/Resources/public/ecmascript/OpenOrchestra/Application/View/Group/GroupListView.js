import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import DuplicateIconListViewMixin  from 'OpenOrchestra/Service/DataTable/Mixin/DuplicateIconListViewMixin'

/**
 * @class GroupListView
 */
class GroupListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin, DuplicateIconListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'group_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "label",
                title: Translator.trans('open_orchestra_group.table.groups.label'),
                orderable: true,
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "nbr_users",
                title: Translator.trans('open_orchestra_group.table.groups.nbr_users'),
                orderable: true,
                visibile: true
            },
            {
                name: "site.name",
                title: Translator.trans('open_orchestra_group.table.groups.site_name'),
                orderable: true,
                visibile: true
            },
            this._getColumnsDefinitionDuplicateIcon()
        ];
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
       return Backbone.history.generateUrl('listGroup', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editGroup', {
            groupId: rowData.get('id'),
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default GroupListView;
