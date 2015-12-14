###*
* @class TextFieldSearchView
###
class TextFieldSearchView extends AbstractSearchFieldView

  events:
    'keyup input.search-column': 'searchColumn'

  ###*
  * required options
  * {
  *   column: {integer} column index
  *   api: {object} DataTable api
  *   domContainer: {object} jquery element
  * }
  * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'columnIndex'
      'domContainer'
      'api'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/textField'
    ]

  ###*
  * @return {this}
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/textField',
        column: @options.columnIndex
        value: @options.api.column(@options.columnIndex).search()
    )
    @insertFieldInHeader()

    return @

(($, OpenOrchestra, DataTable, ViewFieldConfigurator) ->
  ViewFieldConfigurator.text = TextFieldSearchView
  return
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {} ,
  window.OpenOrchestra.DataTable = window.OpenOrchestra.DataTable or {} ,
  window.OpenOrchestra.DataTable.ViewFieldConfigurator = window.OpenOrchestra.DataTable.ViewFieldConfigurator or {}
