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
                title: "Label",
                orderable: true,
                orderDirection: 'desc',
                activateColvis: true,
                visibile: true
            },
            {
                name: "id",
                title: "Id",
                orderable: true,
                orderDirection: 'desc',
                activateColvis: true,
                visibile: true
            },
            {
                name: 'links',
                title: 'Action',
                orderable: false,
                activateColvis: false
            }
        ];
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
