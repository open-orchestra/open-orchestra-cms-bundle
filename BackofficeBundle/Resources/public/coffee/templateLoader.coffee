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
