
showTemplate = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      template = new Template
      template.set response
      new TemplateView(
        template: template
        domContainer: $('#content')
      )
      return
  return
