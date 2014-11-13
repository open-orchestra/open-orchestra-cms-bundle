# JARVIS WIDGETS

$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return

# STATUS WIDGET

$("#widgetStatus").DOMNodeAppear ->
  $("#widget-status-placeholder").replaceWith($("#widgetStatus"))
  return


# CONTENT TITLES

$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return

# NODE STATUS CHANGE

$(document).on "click", ".node-change-status", (event) ->
  url = $(event.currentTarget).data("url")
  language = $(event.currentTarget).data("language")
  version = $(event.currentTarget).data("version")
  statusId = $(event.currentTarget).data("status")
  nodeChangeStatus(url, language, version, statusId)
  return
