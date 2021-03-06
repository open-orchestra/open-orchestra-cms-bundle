import AbstractDataTableView       from 'OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from 'OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from 'OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import DuplicateIconListViewMixin  from 'OpenOrchestra/Service/DataTable/Mixin/DuplicateIconListViewMixin'
import CellFormatterManager        from 'OpenOrchestra/Service/DataFormatter/Manager'
import BooleanFormatter            from 'OpenOrchestra/Service/DataFormatter/BooleanFormatter'
import DateFormatter               from 'OpenOrchestra/Service/DataFormatter/DateFormatter'
import StatusFormatter             from 'OpenOrchestra/Service/DataFormatter/StatusFormatter'

/**
 * @class ContentListView
 */
class ContentListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin, DuplicateIconListViewMixin)
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, urlParameter, contentType}) {
        this._urlParameter = urlParameter;
        this._contentType = contentType;
        super.initialize({collection, settings});
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
        let columnsDefinition = [];
        columnsDefinition.push(this._getColumnsDefinitionDeleteCheckbox());
        columnsDefinition = columnsDefinition.concat(this._generateListableColumn());
        columnsDefinition = columnsDefinition.concat(this._generateFieldColumn());
        columnsDefinition.push(this._getColumnsDefinitionDuplicateIcon());
        columnsDefinition[1].createdCell = this._createEditLink;

        return columnsDefinition;
    }

    /**
     * generate listable columns
     */
    _generateListableColumn() {
        let columnsDefinition = [];
        let defaultListable = this._contentType.get('default_listable');
        let createdCell = {
            'linked_to_site': BooleanFormatter.getType(),
            'created_at'    : DateFormatter.getType(),
            'updated_at'    : DateFormatter.getType(),
            'status'        : StatusFormatter.getType()
        };
        for (let column in defaultListable) {
            if (defaultListable[column]) {
                columnsDefinition.push({
                    name: column,
                    title: Translator.trans('open_orchestra_backoffice.table.contents.' + column),
                    orderable: true,
                    activateColvis: true,
                    visible: true,
                    createdCell: createdCell.hasOwnProperty(column) ? CellFormatterManager.format({type: createdCell[column]}) : undefined
                });
            }
        }
        return columnsDefinition;
    }

    /**
     * generate fields columns
     */
    _generateFieldColumn() {
        let columnsDefinition = [];
        let fields = this._contentType.get('fields');
        if (typeof fields !== "undefined") {
            for (let field of fields) {
                if (field.listable) {
                    columnsDefinition.push({
                        name: "fields." + field.field_id + ".string_value",
                        title: field.label,
                        orderable: field.orderable,
                        activateColvis: true,
                        visible: true,
                        createdCell: CellFormatterManager.format(field)
                    });
                }
            }
        }

        return columnsDefinition;
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listContent', {contentTypeId: this._urlParameter.contentTypeId, language: this._urlParameter.language, contentTypeName: this._urlParameter.contentTypeName, page : page});
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let context = this.data('context');
        let link = Backbone.history.generateUrl('editContent', {
            contentTypeId: context._contentType.get('content_type_id'),
            language: rowData.get('language'),
            contentId: rowData.get('content_id'),
            version: rowData.get('version')
        });

        if (!rowData.get('status').blocked_edition && rowData.get('rights').can_edit) {
            cellData = $('<a>',{
                text: cellData,
                href: '#'+link
            });
        }
        $(td).html(cellData)
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'apiContext': 'list',
            'urlParameter': this._urlParameter
        };
    }
}

export default ContentListView;
