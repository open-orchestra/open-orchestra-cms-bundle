# JARVIS WIDGETS
$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return


# CONTENT TITLES
$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return

$(".modal-dialog").on "resize", (e) ->
  $(this).prev().height($(this).parent().height())
  return
