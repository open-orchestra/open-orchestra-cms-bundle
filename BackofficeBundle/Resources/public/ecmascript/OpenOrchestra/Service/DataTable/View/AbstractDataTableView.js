import OrchestraView from 'OpenOrchestra/Application/View/OrchestraView'

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
        super(options);
        if (this.constructor === AbstractDataTableView) {
            throw TypeError("Can not construct abstract class");
        }
    }

    /**
     * @param {Object} options
     */
    preinitialize(options) {
        this.tagName = 'div';
    }

    /**
     * @param {Object} collection
     * @param {Array}  settings
     */
    initialize({collection, settings}) {
        this.api = null;
        this.$table = null;
        this._tableRegion = this.$el;
        this._collection = collection;
        this._settings = this._resolveSettings(settings);
    }

    /**
     * @returns {AbstractDataTableView}
     */
    render() {
        this.$table = $("<table></table>").data('context', this);
        this.$table.addClass(this._settings.tableClassName);
        this.$table.attr('id', 'dt-' + this.getTableId());
        this._tableRegion.append(this.$table);
        this.api = this.$table.DataTable(this._settings);

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
            tableClassName: 'table table-striped',
            infoCallback: this._infoCallback
        }
    }

    /*
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
        settings.preDrawCallback = settings.preDrawCallback || this._preDrawCallback;
        settings.drawCallback = $.proxy(settings.drawCallback, this) || $.proxy(this._drawCallback, this);

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
            columns.push({'data' : 'attributes.' + element.name.replace('\.', '.attributes.'), 'defaultContent': ''});
            columnDefs.push(_.extend(element, {targets: columnDefs.length}));
        }

        return {columns : columns, columnDefs: columnDefs}
    }

    /**
     * Hide pagination when there is one page
     * @private
     */
    _preDrawCallback() {
        let api = this.api();
        $('.content-pager', $(api.table().container())).show();
        if (api.page.info().recordsDisplay <= api.page.info().length) {
            $('.content-pager', $(api.table().container())).hide();
        }
    }

    /**
     * @param {Object} settings
     *
     * @private
     */
    _drawCallback(settings) {
    }

    /**
     *
     * @returns {string}
     * @private
     */
    _getDomSettings() {
        $.fn.dataTableExt.classes.sPaging = 'content-pager ';
        $.fn.dataTableExt.classes.sLength = 'styled-select pull-right list-length';
        $.fn.dataTableExt.classes.sProcessing = 'spinner';
        $.fn.dataTableExt.classes.sLengthSelect = 'form-control';
        $.fn.dataTable.Buttons.defaults.dom.container.className = 'pull-right';
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-border btn-large';

        let dom = "<'header-results clearfix' <'nb-results pull-left' i>l B p>";
        dom += "<'table-responsive'tr>";
        dom += "p";

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
            if (true === column.orderable) {
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

        if (columns.length > 0) {
            return [{ extend: 'colvis', columns: columns}];
        }

        return [];
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
        let filter = {};
        for (let name of Object.keys(data)) {
            filter[name] = data[name];
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
     * Table summary information display callback
     *
     * @param {Object}  settings
     * @param {Integer} start
     * @param {Integer} end
     * @param {Integer} max
     * @param {Integer} total
     * @param {String}  pre
     * @private
     */
    _infoCallback(settings, start, end, max, total, pre) {
        let totalHtml = "<span>"+total+"</span>";

        return Translator.transChoice('open_orchestra_datatable.info', total, {total: totalHtml});
    }

    /**
     * @returns {Object}
     * @private
     */
    _getLanguage() {
        return {
            "emptyTable":     Translator.trans('open_orchestra_datatable.empty_table'),
            "lengthMenu":     Translator.trans('open_orchestra_datatable.length_menu'),
            "loadingRecords": Translator.trans('open_orchestra_datatable.loading_records'),
            "processing":     "",
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
     * Return options used to fetch collection
     *
     * @returns {{}}
     * @private
     */
    _getSyncOptions() {
        return {};
    }

    /**
     * @return {Function}
     * @private
     */
    _dataTableAjaxCollection() {
        let collection = this._collection;

        return (request, drawCallback, settings) => {
            let options = {
                urlParameter: {},
                data: request,
                processData: true,
                success: (collection) => {
                    settings.sAjaxDataProp = "models";

                    return drawCallback(collection);
                }
            };
            options = $.extend(true, this._getSyncOptions(), options);
            settings.jqXHR = collection.fetch(options);
        }
    }
}
export default AbstractDataTableView
