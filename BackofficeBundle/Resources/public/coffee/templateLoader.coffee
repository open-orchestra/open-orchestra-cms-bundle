$(".template-ajax-load span").click (e) ->
  e.preventDefault()
  url = $(this).parent().data("url")
  templateId = $(this).parent().data("template")
  self.location.hash = templateId
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      template = new Template
      template.set response
      view = new TemplateView(template: template)
      return
  return

$("i.ajax-delete").click (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  if confirm(confirm_text)
    $.ajax
      type: "DELETE"
      url: url
      success: (response) ->
        return
    $(this).parent().parent().hide()
    return
  return
