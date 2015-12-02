DateFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'change input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'column'
      'domContainer'
      'table'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableDateField'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableDateField',
            column : @options.column
    )
    $('.datepicker', @$el).datepicker()
    @insertFieldInHeader()
)

((tableFieldViewconfigurator) ->
  tableFieldViewconfigurator.date = DateFieldSearchView
  return
) window.tableFieldViewconfigurator = window.tableFieldViewconfigurator or {}
