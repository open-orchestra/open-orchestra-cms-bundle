NumberFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'change input.search-column': 'searchColumn'
    'keyup input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'column'
      'domContainer'
      'table'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableNumberField'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableNumberField',
            column : @options.column
    )
    @insertFieldInHeader()
)

((tableFieldViewconfigurator) ->
  tableFieldViewconfigurator.number = NumberFieldSearchView
  return
) window.tableFieldViewconfigurator = window.tableFieldViewconfigurator or {}
