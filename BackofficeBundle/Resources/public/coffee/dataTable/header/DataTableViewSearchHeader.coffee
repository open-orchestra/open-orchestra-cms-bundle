###*
 * @class DataTableViewSearchHeader
###
class DataTableViewSearchHeader extends OrchestraView

  tagName: "tr"

  className: "header-search-input"

  ###*
   * required options
   * {
   *   api: {object} DataTable api
   *   domContainer: {object} jquery element
   * }
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'api'
      'domContainer'
    ])
    @options.api.on 'column-visibility.dt', @toggleColumn*

    # This view not needed these templates, there are loaded here to optimize number of request
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/booleanField'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/textField'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/numberField'
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/dateField'
    ]

  ###*
   * @return {this}
  ###
  render: ->
    columnsDefs = @options.api.settings()[0].aoColumns
    viewField = window.OpenOrchestra.DataTable.ViewFieldConfigurator
    for columnDefs in columnsDefs
        type = "empty"
        if columnDefs.searchField? and viewField[columnDefs.searchField]?
            type = columnDefs.searchField
        viewClass = viewField[type]
        index = if columnDefs.targets >= 0 then columnDefs.targets else columnsDefs.length - 1
        new viewClass(@addOption(
            columnIndex : index
            api : @options.api
            domContainer : @$el
        ))
    @options.domContainer.append(@$el)

    return @

  ###*
   * @param {object} e jquery event
   * @param {Object} settings DataTable settings
   * @param {integer} column column index
   * @param {boolean} state column visibility
  ###
  toggleColumn: (e, settings, column, state) ->
    $('td', @$el).eq(column).toggle();
