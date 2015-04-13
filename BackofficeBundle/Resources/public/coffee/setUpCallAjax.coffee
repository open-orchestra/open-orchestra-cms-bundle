$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      context = settings.context
      displayLoader(context.button) if context != undefined && context.button != undefined
  $(document).ajaxError (event, jqXHR, settings) ->
    if isAccessDenied(jqXHR.responseText)
      redirectToLogin()
  $(document).ajaxSuccess (event, xhr, settings) ->
    if isLoginForm(xhr.responseText)
      redirectToLogin()
