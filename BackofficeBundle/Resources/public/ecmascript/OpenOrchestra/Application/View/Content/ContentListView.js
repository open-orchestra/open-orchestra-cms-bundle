import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class ContentListView
 */
class ContentListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, urlParameter, contentType}) {
        super.initialize({collection, settings});
        this._urlParameter = urlParameter;
        this._contentType = contentType;
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'content_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        let columnsDefinition = [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.contents.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "updated_at",
                title: Translator.trans('open_orchestra_backoffice.table.contents.updated_at'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
            },
            {
                name: "created_by",
                title: Translator.trans('open_orchestra_backoffice.table.contents.created_by'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
            },
        ];

        columnsDefinition.push({
            name: "duplicate",
            title: Translator.trans('open_orchestra_backoffice.table.contents.duplicate'),
            orderable: false,
            width: '20px',
            createdCell: this._createDuplicateIcon
        });
        
        return columnsDefinition;
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listContent', {contentType: this._urlParameter.contentType, language: this._urlParameter.language, contentTypeName: this._urlParameter.contentTypeName, page : page});
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
        let attributes = {type: 'checkbox', id: id, class:'delete-checkbox'};
        let $checkbox = $('<input>', attributes);
        if (rowData.get('rights').can_delete && !rowData.get('used')) {
            $checkbox.prop("disabled", true);
        }
        $checkbox.data(rowData);
        $(td).append($checkbox);
        $(td).append($('<label>', {for: id}))
    }

    /**
    *
    * @param {Object} td
    * @param {Object} cellData
    * @param {Object} rowData
    *
    * @private
    */
   _createDuplicateIcon(td, cellData, rowData) {
       if (rowData.get('rights').can_create && rowData.get('content_type').defining_versionable) {
           let $icon = $('<i>', {'aria-hidden': 'true', class:'clone-icon fa fa-clone'});
           $icon.data(rowData);
           $(td).append($icon);
       }
   }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        if (rowData.get('rights').can_edit && !rowData.get('status').blocked_edition) {
            cellData = $('<a>',{
                text: cellData,
                href: '#'
            });
        }
        $(td).html(cellData)
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'urlParameter': {
                'contentType': this._urlParameter.contentType,
                'siteId': this._urlParameter.siteId,
                'language': this._urlParameter.language
            }
        };
    }
}

export default ContentListView;
