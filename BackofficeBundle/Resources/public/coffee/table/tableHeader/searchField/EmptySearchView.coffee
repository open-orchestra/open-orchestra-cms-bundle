EmptySearchView = OrchestraView.extend(

  tagName: "td"

  initialize: (options) ->
    @options = @reduceOption(options, [
      'column'
      'domContainer'
      'table'
    ])
    @doRender()
    return

  render: ->
    @$el.attr('data-column', @options.column)
    @options.domContainer.append(@$el)
)

((tableFieldViewconfigurator) ->
  tableFieldViewconfigurator.empty = EmptySearchView
  return
) window.tableFieldViewconfigurator = window.tableFieldViewconfigurator or {}
