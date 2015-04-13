$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      context = settings.context
      displayLoader(context.button) if context != undefined && context.button != undefined
  $(document).ajaxError (event, jqXHR, settings) ->
    redirectToLogin() if isAccessDenied(jqXHR.responseText)
  $(document).ajaxSuccess (event, xhr, settings) ->
    redirectToLogin() if isLoginForm(xhr.responseText)
