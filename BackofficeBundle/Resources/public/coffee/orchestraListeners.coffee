# JARVIS WIDGETS

$(".widget-grid").DOMNodeAppear ->
  setup_widgets_desktop()
  return

# CONTENT TITLES

$(".page-title").DOMNodeAppear ->
  renderPageTitle()
  return

# NODE STATUS CHANGE

$(document).on "click", ".node-change-status", (event) ->
  url = $(event.currentTarget).data("url")
  statusId = $(event.currentTarget).data("status")
  nodeChangeStatus(url, statusId)
  return

#CONTENT TYPE ID

$(document).on "click", "#content_type_submit", (event) ->
  if $('#content_type_contentTypeId').val().length is 0
    contentTypeId = recupInput($("#content_type_names input[type=text]"))
    $('#content_type_contentTypeId').val(contentTypeId.latinise().replace(/[^a-z0-9]/gi,'_'))

recupInput = (el) ->
  for i in el
    return i.value if i.value
