# JARVIS WIDGETS
$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return


# CONTENT TITLES
$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return

# SELECT2
$("select.select2").DOMNodeAppear ->
  $(this).select2()
  return


# NODE STATUS CHANGE
$(document).on "click", ".node-change-status", (event) ->
  url = $(event.currentTarget).data("url")
  statusId = $(event.currentTarget).data("status")
  nodeChangeStatus(url, statusId)
  return
