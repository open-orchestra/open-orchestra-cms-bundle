#--[ Jarvis widgets ]----------#

$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return


#--[ Content titles ]----------#

$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return
