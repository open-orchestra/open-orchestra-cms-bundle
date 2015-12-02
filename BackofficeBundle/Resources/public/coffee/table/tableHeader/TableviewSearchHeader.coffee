TableviewSearchHeader = OrchestraView.extend(

  tagName: "tr"
  className: "header-search-input"

  initialize: (options) ->
    @options = @reduceOption(options, [
      'table'
      'domContainer'
      'inputHeader'
    ])
    widgetChannel.trigger 'ready', @
    @render()
    @options.table.on 'column-visibility.dt', @toggleColumn
    return

  render: ->
    columnsDefs = @options.table.fnSettings().aoColumns
    for columnDefs in columnsDefs
        type = "empty"
        if columnDefs.searchField? and window.tableFieldViewconfigurator[columnDefs.searchField]?
            type = columnDefs.searchField
        viewClass = window.tableFieldViewconfigurator[type]
        new viewClass(@addOption(
            column : columnDefs.targets
            table : @options.table
            domContainer : @$el
        ))
    @options.domContainer.append(@$el)

  toggleColumn: (e, settings, column, state) ->
    $('td', @$el).eq(column).toggle();
)
