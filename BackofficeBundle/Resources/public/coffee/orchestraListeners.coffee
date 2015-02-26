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

# CLOSE MODALS
$(".close", ".modal-header").click ->
  $("#select2-drop-mask").click();
  return
