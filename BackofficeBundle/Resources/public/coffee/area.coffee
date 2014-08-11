
class Area
  constructor: (@areaResponse)->
    @blocks = []
    @subAreas = []
    for block of @areaResponse.blocks
      @addBlock(@areaResponse.blocks[block])
    for subArea of @areaResponse.areas
      @addSubArea(@areaResponse.areas[subArea])
  addBlock: (block) ->
    @blocks.push new Block block
  addSubArea: (subArea) ->
    @subAreas.push new Area subArea
  renderTitle: ->
    return '<span class="title">' + @areaResponse.ui_model.label + '</span>'
  renderActionButton: ->
    return '<span class="action"><i class="fa fa-cog"></i></span>'
  renderPreview: ->
    return '<span class="preview"></span>'
  printHtml: ->
    returnString = '<div>'
    returnString += @renderTitle()
    returnString += @renderActionButton()
    returnString += @renderPreview()
    returnString += '<ul class="ui-model-blocks">'
    if @blocks.length > 0
      height = 100 / @blocks.length
      for block of @blocks
        returnString += '<li class="ui-model-blocks block" style="height: ' + height + '%;">'
        returnString += @blocks[block].printHtml()
        returnString += '</li>'
      returnString += '</div>'
    if @subAreas.length > 0
      height = 100 / @subAreas.length
      returnString += '<ul class="ui-model-areas">'
      for subArea of @subAreas
        returnString += '<li class="ui-model-areas inline" style="width: ' + height + '%;">'
        returnString += @subAreas[subArea].printHtml()
        returnString += '</li>'
      returnString += '</ul>'

    return returnString

root = exports ? window
root.Area = Area