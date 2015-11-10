BooleanFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'change select.search-column': 'searchColumn'

  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableBooleanField'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableHeader/searchField/tableBooleanField',
            column : @options.column
    )
    @insertFieldInHeader()
)

tableFieldViewConfigurator.boolean = BooleanFieldSearchView