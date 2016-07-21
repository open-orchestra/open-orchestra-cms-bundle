$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      context = settings.context
      displayLoader(context.button) if context != undefined && context.button != undefined
  $(document).ajaxError (event, jqXHR, settings) ->
    errors = {error : {message :$('#content').data('error-txt')}}
    statusCode = jqXHR.status
    console.log jqXHR.statusText
    # check if xhr is an abort or if an error callback is override for the xhr status code
    if jqXHR.statusText != 'abort' && not (settings.statusCode? && settings.statusCode[statusCode]?)
      if isAccessDenied(jqXHR)
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
    redirectToLogin() if isLoginForm(xhr)
