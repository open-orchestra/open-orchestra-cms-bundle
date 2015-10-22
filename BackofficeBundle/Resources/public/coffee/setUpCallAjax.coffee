xhrFifo = []
$(document).ready ->
  $.ajaxSetup
    url: 'defaultUrl',
    beforeSend: (xhr, settings) ->
      if settings.url == 'defaultUrl'
        xhr.abort()
        redirectUrl = appRouter.generateUrl 'showDashboard'
        displayMenu(redirectUrl)
      else
        xhrFifo.push(xhr)
        context = settings.context
        displayLoader(context.button) if context != undefined && context.button != undefined
    abortXhr: ->
      for i of xhrFifo
        xhrFifo[i].abort()
      xhrFifo = []
  $(document).ajaxError (event, jqXHR, settings) ->
    errors = {error : {message :$('#content').data('error-txt')}}
    if jqXHR.statusText != 'abort'
      if isAccessDenied(jqXHR.responseText)
        redirectToLogin()
      else if jqXHR.responseJSON
        errors = jqXHR.responseJSON
      else if jqXHR.responseText and jqXHR.responseText != ''
        errors = {error : {message :jqXHR.responseText}}
      else if settings.message
        errors = {error : {message :settings.message}}
      viewClass = appConfigurationView.getConfiguration('status', 'apiError')
      new viewClass(
        errors: errors
        domContainer: $('h1.page-title').parent()
      )
  $(document).ajaxSuccess (event, xhr, settings) ->
    redirectToLogin() if isLoginForm(xhr.responseText)
