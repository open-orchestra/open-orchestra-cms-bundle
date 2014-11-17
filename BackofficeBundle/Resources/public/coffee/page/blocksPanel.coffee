$(document).on "click", ".js-widget-blockpanel .header", (event) ->
  $(this).parent().toggleClass "activate"
  $('#content .jarviswidget > div').toggleClass "panel-activate"
  $(this).effect "highlight", {}, 500
  makeSortable ".js-widget-blockpanel .ui-model", true if $(this).parent().hasClass("activate")
  event.preventDefault()
  return
$(document).on "mouseover", ".js-widget-blockpanel .ui-model li", (event) ->
  $(this).addClass "hover"
$(document).on "mouseout", ".js-widget-blockpanel .ui-model li", (event) ->
  $(this).removeClass "hover"
$(document).on "mousedown", ".js-widget-blockpanel .ui-model li", (event) ->
  $(this).removeClass "hover"
