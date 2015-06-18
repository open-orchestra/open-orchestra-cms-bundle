showTemplate = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      template = new TemplateModel
      template.set response
      templateViewClass = appConfigurationView.getConfiguration('template', 'showTemplate')
      new templateViewClass(
        template: template
        domContainer: $('#content')
      )
      return
  return
