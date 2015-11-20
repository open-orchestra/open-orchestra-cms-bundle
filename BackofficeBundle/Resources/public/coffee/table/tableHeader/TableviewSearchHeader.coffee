TableviewSearchHeader = OrchestraView.extend(

  tagName: "tr"
  className: "header-search-input"

  initialize: (options) ->
    @options = options
    @render()
    widgetChannel.trigger 'ready', @
    return

  render: ->
    for type, column in @options.inputHeader
        type = "empty" if not window.tableFieldViewconfigurator[type]?
        viewClass = window.tableFieldViewconfigurator[type]
        new viewClass(@addOption(
            column : column
            apiTable : @options.apiTable
            domContainer : @$el
        ))

    @options.domContainer.append(@$el)
)

((tableFieldViewconfigurator) ->
  tableFieldViewconfigurator.empty = EmptySearchView
  return
) window.tableFieldViewconfigurator = window.tableFieldViewconfigurator or {}
