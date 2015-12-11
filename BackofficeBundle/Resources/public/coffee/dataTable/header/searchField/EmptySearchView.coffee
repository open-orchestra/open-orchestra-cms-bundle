###*
* @class EmptySearchView
###
class EmptySearchView extends AbstractSearchFieldView

  tagName: "td"

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
    @doRender()

  ###*
  * @return {this}
  ###
  render: ->
    @$el.attr('data-column', @options.columnIndex)
    @insertFieldInHeader()

    return @


((tableFieldViewConfigurator) ->
  tableFieldViewConfigurator.empty = EmptySearchView
  return
) window.tableFieldViewConfigurator = window.tableFieldViewConfigurator or {}
