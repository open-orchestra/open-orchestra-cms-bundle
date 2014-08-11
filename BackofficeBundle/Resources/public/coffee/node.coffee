
class Node
  constructor: (@nodeResponse)->
    @areas = []
    for area of @nodeResponse.areas
      @addArea @nodeResponse.areas[area]
  addArea: (area) ->
    @areas.push new Area area
  renderTitle: ->
    return '<span class="title">' + @nodeResponse.name + '</span>'
  renderActionButton: ->
    return '<span class="action"><i class="fa fa-cog"></i></span>'
  renderPreview: ->
    return '<span class="preview"></span>'
  printHtml: ->
    returnString = '<div class="ui-model">'
    returnString += @renderTitle()
    returnString += @renderPreview()
    returnString += @renderActionButton()
    if @areas.length > 0
      height = 100 / @areas.length
      returnString += '<ul class="ui-model-areas">'
      for area in @areas
        returnString += '<li class="ui-model-areas block" style="height: 50%;">'
        returnString += area.printHtml()
        returnString += '</li>'
    returnString += '</div>'

root = exports ? window
root.Node = Node