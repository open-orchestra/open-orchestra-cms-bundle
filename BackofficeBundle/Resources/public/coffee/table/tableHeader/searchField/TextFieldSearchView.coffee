TextFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'keyup input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'column'
      'domContainer'
      'table'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableTextField'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableTextField',
        column : @options.column
    )
    @insertFieldInHeader()
)

((tableFieldViewconfigurator) ->
  tableFieldViewconfigurator.text = TextFieldSearchView
  return
) window.tableFieldViewconfigurator = window.tableFieldViewconfigurator or {}
