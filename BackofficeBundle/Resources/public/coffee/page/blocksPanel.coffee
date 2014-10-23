$(document).on "click", ".blocks-panel-setting", (event) ->
  $(this).parent().toggleClass "activate"
  $('#content .jarviswidget > div').toggleClass "panel-activate"
  $(this).effect "highlight", {}, 500
  makeSortable ".blocks-panel", true if $(this).parent().hasClass("activate")
  event.preventDefault()
  return
$(document).on "mouseover", "div.blocks-panel li", (event) ->
  $(this).addClass "hover"
$(document).on "mouseout", "div.blocks-panel li", (event) ->
  $(this).removeClass "hover"
$(document).on "mousedown", "div.blocks-panel li", (event) ->
  $(this).removeClass "hover"
