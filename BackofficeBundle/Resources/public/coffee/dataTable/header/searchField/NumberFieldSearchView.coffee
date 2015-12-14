###*
* @class NumberFieldSearchView
###
class NumberFieldSearchView extends AbstractSearchFieldView

  events:
    'change input.search-column': 'searchColumn'
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
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/numberField'
    ]

  ###*
  * @return {this}
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/numberField',
        column : @options.columnIndex
        value: @options.api.column(@options.columnIndex).search()
    )
    @insertFieldInHeader()

    return @

(($, OpenOrchestra) ->
  OpenOrchestra.DataTable = {} if not OpenOrchestra.DataTable?
  OpenOrchestra.DataTable.ViewFieldConfigurator = {} if not OpenOrchestra.DataTable.ViewFieldConfigurator?
  OpenOrchestra.DataTable.ViewFieldConfigurator.number = NumberFieldSearchView
  return
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
