
showTemplate = (url)->
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        template = new Template
        template.set response
        view = new TemplateView(template: template)
        appRouter.setCurrentMainView(view)
        return
  return
