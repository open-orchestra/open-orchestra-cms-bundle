###*
* @class BooleanFieldSearchView
###
class BooleanFieldSearchView extends AbstractSearchFieldView

  events:
    'change select.search-column': 'searchColumn'

  ###*
  * required options
  * {
  *   api: {object} DataTable api
  *   domContainer: {object} jquery element
  *   column: {integer} column index
  * }
  * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'api'
      'domContainer'
      'columnIndex'
    ])
    @loadTemplates ['OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/booleanField']

  ###*
  * @return {this}
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/booleanField',
        column : @options.columnIndex
    )
    $('.search-column', @$el).val(@options.api.column(@options.columnIndex).search())
    @insertFieldInHeader()

    return @

(($, OpenOrchestra, DataTable, ViewFieldConfigurator) ->
  ViewFieldConfigurator.boolean = BooleanFieldSearchView
  return
) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {} ,
  window.OpenOrchestra.DataTable = window.OpenOrchestra.DataTable or {} ,
  window.OpenOrchestra.DataTable.ViewFieldConfigurator = window.OpenOrchestra.DataTable.ViewFieldConfigurator or {}
