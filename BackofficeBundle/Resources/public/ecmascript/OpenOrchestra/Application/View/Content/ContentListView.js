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
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click .clone-icon'] = '_clickDuplicateIcon';
    }

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
        let columnsDefinition = [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "name",
                title: Translator.trans('open_orchestra_backoffice.table.contents.name'),
                orderable: true,
                orderDirection: 'desc',
                createdCell: this._createEditLink
            },
            {
                name: "updated_at",
                title: Translator.trans('open_orchestra_backoffice.table.contents.updated_at'),
                orderable: true,
                activateColvis: true
            },
            {
                name: "created_by",
                title: Translator.trans('open_orchestra_backoffice.table.contents.created_by'),
                orderable: true,
                activateColvis: true
            },
        ];

        let fields = this._contentType.get('fields');

        for (let field of fields) {
            if (field.listable) {
                columnsDefinition.push({
                    name: "fields." + field.field_id + ".string_value",
                    title: field.label,
                    orderable: true,
                    activateColvis: true,
                    visible: false
                });
            }
        }

        columnsDefinition.push({
            name: "duplicate",
            title: Translator.trans('open_orchestra_backoffice.table.contents.duplicate'),
            orderable: false,
            width: '20px',
            createdCell: $.proxy(this._createDuplicateIcon, this.api, this._contentType),
        });

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
     * @param {Object} rowData
     *
     * @private
     */
    _createCheckbox(td, cellData, rowData) {
        let id = 'checkbox' + rowData.cid;
        let attributes = {type: 'checkbox', id: id, class:'delete-checkbox'};
        let $checkbox = $('<input>', attributes);
        if (!rowData.get('rights').can_delete || rowData.get('used')) {
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
    * @param {Object} contentType
    *
    * @private
    */
   _createDuplicateIcon(contentType, td, cellData, rowData) {
       if (rowData.get('rights').can_create && contentType.get('defining_versionable')) {
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
     * @param {Object} event
     *
     * @private
     */
    _clickDuplicateIcon(event) {
        let content = $(event.currentTarget).data();
        content = this._collection.findWhere({'id': content.get('id')});

        content.sync('create', content, {
            success: () => {
                this.api.draw(false);
            }
        });
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'urlParameter': {
                'contentTypeId': this._urlParameter.contentTypeId,
                'siteId': this._urlParameter.siteId,
                'language': this._urlParameter.language
            }
        };
    }
}

export default ContentListView;
