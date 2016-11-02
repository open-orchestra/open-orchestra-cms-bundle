import OrchestraView from '../../../Application/View/OrchestraView'

/**
 * @class AbstractDataTableView
 */
class AbstractDataTableView extends OrchestraView
{
    /**
     * Constructor
     *
     * @param {Object} options
     */
    constructor(options) {
        if (new.target === DataTableView) {
            throw TypeError("Can not construct abstract class");
        }
        super(options);
    }

    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.tagName = 'div';
        this.api = null;
        this.table = null;
    }

    /**
     * @inheritdoc
     */
    initialize({collection, settings}) {
        this._collection = collection;
        this._settings = this._resolveSettings(settings);
    }

    /**
     * @returns {AbstractDataTableView}
     */
    render() {
        this.$table = $("<table></table>");
        this.$table.addClass(this._settings.tableClassName);
        this.$table.attr('id', 'dt-' + this.getTableId());
        this.$el.append(this.$table);

        this.$table.DataTable(this._settings);

        return this;
    }

    /**
     * Get table id - Used to identified the table.
     */
    getTableId() {
        throw new TypeError("Please implement abstract method getTableId.");
    }

    /**
     * Describe dataTable column
     * [
     *  {
     *    name:  'name'
     *    title: 'title'
     *    orderable: true,
     *    orderDirection: 'asc'
     *  }
     * ]
     *
     * @returns {Array}
     */
    getColumnsDefinition() {
        throw new TypeError("Please implement abstract method getColumnsDefinition.");
    }

    /**
     * Return settings of DataTableView
     *
     * @return {Object}
     */
    getSettings() {
        return {
            serverSide: true,
            processing: true,
            pageLength: 10,
            page: 0,
            searching: true,
            ordering: true,
            orderCellsTop: true,
            autoWidth: false,
            stateSave: false,
            pagingType: 'numbers',
            globalSearch: false,
            tableClassName: 'table table-striped table-bordered table-hover smart-form'
        }
    }

    /**
     * Add filter to the datatable and reload data
     * Work only with server side, use the default method search for other case
     * {
     *  name : value,
     *  otherName : otherValue
     * }
     * @param {object} filters
     */
    filter(filters)
    {
        if (null !== this.$table) {
            this.$table.data('filter', filters);
            this.$table.DataTable().draw();
        }
    }

    /**
     * @param {Object} settings
     *
     * @returns {Object}
     * @private
     */
    _resolveSettings(settings) {
        settings = _.extend(this.getSettings(), settings);
        settings = _.extend(settings, this._parseTableColumnDefs());

        settings.displayStart = settings.displayStart || settings.pageLength * settings.page;
        settings.buttons = this._getButtonsSettings(settings);

        if (true === settings.serverSide) {
            _.extend(settings, this._getServerSideSettings(settings));
        }

        settings.dom = this._getDomSettings();

        settings.order = this._getOrderSettings(settings);
        if (settings.order.length == 0) {
            settings.ordering = false;
        }
        settings.language = this._getLanguage();

        return settings
    }

    /**
     * @returns {{columns: Array, columnDefs: Array}}
     * @private
     */
    _parseTableColumnDefs() {
        let columns = [];
        let columnDefs = [];
        let columnsParameters = this.getColumnsDefinition();
        for (let element of columnsParameters) {
            columns.push({'data' : 'attributes.' + element.name, 'defaultContent': ''});
            columnDefs.push(_.extend(element, {targets: columnDefs.length}));
        }

        return {columns : columns, columnDefs: columnDefs}
    }

    /**
     *
     * @returns {string}
     * @private
     */
    _getDomSettings() {
        let dom = "<'row dt-toolbar'";
        dom += "<'col-sm-11 col-xs-6 hidden-xs'B><'col-xs-12 col-sm-1 hidden-xs'l>>";
        dom += "tr";
        dom += "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>";

        return dom;
    }

    /**
     * @param {Object} settings
     *
     * @returns {Array}
     * @private
     */
    _getOrderSettings(settings) {
        let order = [];
        for (let column of settings.columnDefs) {
            if (true === column.orderable && undefined !== column.orderDirection) {
                order.push([column.targets, column.orderDirection])
            }
        }

        return order;
    }

    /**
     * @param {Object} settings
     *
     * @returns {Array}
     * @private
     */
    _getButtonsSettings(settings) {
        let columns = [];
        for (let column of settings.columnDefs) {
            if (column.activateColvis) {
                columns.push(column.targets);
            }
        }

        return [{ extend: 'colvis', columns: columns}];
    }

    /**
     * @param {Object} settings
     *
     * @returns {Object}
     * @private
     */
    _getServerSideSettings(settings) {
        let serverSideSettings = {};

        if (undefined === settings.ajax) {
            serverSideSettings.ajax = this._dataTableAjaxCollection();
        }
        if (undefined === settings.serverParams) {
            serverSideSettings.serverParams = $.proxy((data) => {
                let dataFilter = this.$table.data('filter');
                if ('undefined' !== typeof dataFilter) {
                    this.$table.removeData('filter');
                    data.search = this._transformerDataFilter(dataFilter);
                }
                data.order = this._transformDataOrder(data);

                delete data.columns;
                delete data.draw;
            }, this);
        }

        return serverSideSettings;
    }

    /**
     * @param {Object} data
     *
     * @returns {Object}
     * @private
     */
    _transformerDataFilter(data) {
        let filter = {
            columns : {}
        };
        for (let name of Object.keys(data)) {
            filter.columns[name] = data[name];
        }

        return filter;
    }

    /**
     * @param {object} data
     *
     * @returns {object|null}
     * @private
     */
    _transformDataOrder(data) {
        for (let order of data.order) {
            if (undefined !== data.columns[order.column] && data.columns[order.column].orderable == true) {
                return { name: data.columns[order.column].name, dir: order.dir }
            }
        }

        return null;
    }

    /**
     * @returns {Object}
     * @private
     */
    _getLanguage() {
        return {
            "emptyTable":     Translator.trans('open_orchestra_datatable.empty_table'),
            "info":           Translator.trans('open_orchestra_datatable.info'),
            "infoEmpty":      Translator.trans('open_orchestra_datatable.info_empty'),
            "infoFiltered":   Translator.trans('open_orchestra_datatable.info_filtered'),
            "lengthMenu":     Translator.trans('open_orchestra_datatable.length_menu'),
            "infoThousands":  Translator.trans('open_orchestra_datatable.info_thousands'),
            "loadingRecords": Translator.trans('open_orchestra_datatable.loading_records'),
            "processing":     Translator.trans('open_orchestra_datatable.processing'),
            "search":         Translator.trans('open_orchestra_datatable.search'),
            "zeroRecords":    Translator.trans('open_orchestra_datatable.zero_records'),
            "paginate": {
                "first":      Translator.trans('open_orchestra_datatable.paginate.first'),
                "last":       Translator.trans('open_orchestra_datatable.paginate.last'),
                "next":       Translator.trans('open_orchestra_datatable.paginate.next'),
                "previous":   Translator.trans('open_orchestra_datatable.paginate.previous')
            },
            "buttons": {
                "colvis": Translator.trans('open_orchestra_datatable.buttons.colvis')
            }
        }
    }

    /**
     * @return {Function}
     * @private
     */
    _dataTableAjaxCollection() {
        let collection = this._collection;
        return (request, drawCallback, settings) => {
           settings.jqXHR = collection.fetch({
                data: request,
                processData: true,
                success: (collection) => {
                    settings.sAjaxDataProp = "models";

                    return drawCallback(collection);
                }
            });
        }
    }

}

export default AbstractDataTableView
