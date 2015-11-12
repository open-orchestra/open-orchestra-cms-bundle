TextFieldSearchView = AbstractSearchFieldView.extend(

  events:
    'keyup input.search-column': 'searchColumn'

  initialize: (options) ->
    @options = options
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

tableFieldViewConfigurator.text = TextFieldSearchView
