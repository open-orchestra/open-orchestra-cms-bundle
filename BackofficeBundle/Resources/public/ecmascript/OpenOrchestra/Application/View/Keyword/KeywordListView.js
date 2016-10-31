import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'

/**
 * @class KeywordListView
 */
class KeywordListView extends AbstractDataTableView
{
    /**
     * @inheritDoc
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events = {
            'draw.dt table': '_updatePage'
        };
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'keyword_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "label",
                title: Translator.trans('open_orchestra_backoffice.table.keywords.label'),
                orderable: true,
                orderDirection: 'desc',
                activateColvis: true,
                visibile: true
            },
            {
                name: 'links',
                orderable: false,
                activateColvis: false,
                createdCell: this._addLinkCell
            }
        ];
    }

    /**
     *
     * @param td
     * @param cellData
     *
     * Example of specif column
     * @private
     */
    _addLinkCell(td, cellData) {
        $(td).html('<a href="' + cellData._self_form + '">Edit</a>');
    }

    /**
     * @param {Object} event
     *
     * @private
     */
    _updatePage(event) {
        let api = $(event.target).DataTable();
        let page = api.page.info().page + 1;
        let url = Backbone.history.generateUrl('listKeyword', {page : page});
        Backbone.history.navigate(url);
    }
}

export default KeywordListView;
