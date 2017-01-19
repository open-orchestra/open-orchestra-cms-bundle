import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import DuplicateIconListViewMixin  from '../../../Service/DataTable/Mixin/DuplicateIconListViewMixin'

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
        columnsDefinition = columnsDefinition.concat(this._generateListableColumn())
        columnsDefinition = columnsDefinition.concat(this._generateFieldColumn())
        columnsDefinition.push(this._getColumnsDefinitionDuplicateIcon());
        columnsDefinition[1].orderDirection = 'desc';
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
            'linked_to_site': this._createBoolean,
        };
        for (let column in defaultListable) {
            if (defaultListable[column]) {
                columnsDefinition.push({
                    name: column,
                    title: Translator.trans('open_orchestra_backoffice.table.contents.' + column),
                    orderable: true,
                    activateColvis: true,
                    visible: true,
                    createdCell: createdCell.hasOwnProperty(column) ? createdCell[column].bind(this) : void(0)
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
        let createdCell = {};
        for (let field of fields) {
            if (field.listable) {
                columnsDefinition.push({
                    name: "fields." + field.field_id + ".string_value",
                    title: field.label,
                    orderable: field.orderable,
                    activateColvis: true,
                    visible: true,
                    createdCell: createdCell.hasOwnProperty(field.type) ? createdCell[field.type].bind(this) : void(0)
                });
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
     * @inheritDoc
     */
    _canDelete(rowData) {
        return rowData.get('rights').hasOwnProperty('can_delete') &&
            rowData.get('rights').can_delete &&
            !rowData.get('used');
    }

    /**
     * @inheritDoc
     */
    _canDuplicate(rowData) {
        return rowData.get('rights').hasOwnProperty('can_create') && rowData.get('rights').can_create  && this._contentType.get('defining_versionable');      
    }

    /**
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
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createBoolean(td, cellData, rowData) {
        let $icon = $('<i>', {'aria-hidden': 'true'});
        if(cellData) {
            $icon.addClass('fa fa-check text-success');
        } else {
            $icon.addClass('fa fa-close text-danger');
        } 
        
        $(td).html($icon)
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'urlParameter': this._urlParameter
        };
    }
}

export default ContentListView;
