DateFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'change input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = options
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

tableFieldViewConfigurator.date = DateFieldSearchView