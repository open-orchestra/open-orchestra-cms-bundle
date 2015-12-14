###*
* @class DateFieldSearchView
###
class DateFieldSearchView extends AbstractSearchFieldView

  events:
    'change input.search-column': 'searchColumn'

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
    @loadTemplates ['OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/dateField']

  ###*
  * @return {this}
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/dateField',
        column : @options.columnIndex
        value: @options.api.column(@options.columnIndex).search()
    )
    @insertFieldInHeader()

    return @


(($, OpenOrchestra) ->
  OpenOrchestra.DataTable = {} if not OpenOrchestra.DataTable?
  OpenOrchestra.DataTable.ViewFieldConfigurator = {} if not OpenOrchestra.DataTable.ViewFieldConfigurator?
  OpenOrchestra.DataTable.ViewFieldConfigurator.date = DateFieldSearchView
  return
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
