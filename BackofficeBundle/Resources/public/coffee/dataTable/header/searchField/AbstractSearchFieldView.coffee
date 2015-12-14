###*
* @class AbstractSearchFieldView
###
class AbstractSearchFieldView extends OrchestraView

  ###*
  * @param {object} event jquery event
  ###
  searchColumn : (event) ->
    value = $(event.target).val()
    @options.api.column(@options.columnIndex).search(value).draw()

  ###*
  * Insert field in DataTable header
  ###
  insertFieldInHeader : () ->
    @$el.hide() if false == @options.api.column(@options.columnIndex).visible()
    for td in @options.domContainer.children('td')
        if parseInt($(td).data('column')) > @options.columnIndex
          @$el.insertBefore($(td))
          return
    @options.domContainer.append(@$el)
