class Block
  constructor: (@blockResponse)->
  renderTitle: ->
    return '<span class="title">' + @blockResponse.ui_model.label + '</span>'
  renderActionButton: ->
    return '<span class="action"><i class="fa fa-cog"></i></span>'
  renderPreview: ->
    return '<span class="preview"><div>' + @blockResponse.ui_model.html + '<br>Title : ' + @blockResponse.attributes.title + '<br>Author : ' + @blockResponse.attributes.author + '<br>News : ' + @blockResponse.attributes.news + '<br></div></span>'
  printHtml: ->
    return '<div class="' + @blockResponse.method + '">' + @renderTitle() + @renderActionButton() + @renderPreview() + '</div>'


root = exports ? window
root.Block = Block
