$(".template-ajax-load span").click (e) ->
  e.preventDefault()
  url = $(this).parent().data("url")
  templateId = $(this).parent().data("template")
  self.location.hash = templateId
  return
