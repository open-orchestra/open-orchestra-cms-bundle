$(document).on "click", ".js-widget-blockpanel .widget-toolbar", (event) ->
  $(this).parent().toggleClass "activate"
  $('#content .jarviswidget > div').toggleClass "panel-activate"
  $(this).effect "highlight", {}, 500
  makeSortable ".js-widget-blockpanel", true if $(this).parent().hasClass("activate")
  event.preventDefault()
  return
$(document).on "mouseover", ".js-widget-blockpanel li", (event) ->
  $(this).addClass "hover"
$(document).on "mouseout", ".js-widget-blockpanel li", (event) ->
  $(this).removeClass "hover"
$(document).on "mousedown", ".js-widget-blockpanel li", (event) ->
  $(this).removeClass "hover"
