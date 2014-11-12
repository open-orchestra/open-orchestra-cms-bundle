# JARVIS WIDGETS

$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return

# Status Wigdet
$("#widgetStatus").DOMNodeAppear ->
  $("#widget-status-placeholder").replaceWith($("#widgetStatus"))
  return


# CONTENT TITLES

$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return
