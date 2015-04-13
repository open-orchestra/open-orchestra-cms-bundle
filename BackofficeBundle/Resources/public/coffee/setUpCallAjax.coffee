$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      context = settings.context
      if context != undefined && context.isSave == true
        if context.button != undefined
          displayLoader(context.button)
          console.log context.button
  $(document).ajaxError (event, jqXHR, settings) ->
    if isAccessDenied(jqXHR.responseText)
      redirectToLogin()
  $(document).ajaxSuccess (event, xhr, settings) ->
    if isLoginForm(xhr.responseText)
      redirectToLogin()
