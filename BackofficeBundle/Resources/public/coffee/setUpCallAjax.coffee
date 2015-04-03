$(document).ready ->
  $.ajaxSetup
    beforeSend: (event) ->
      if event != undefined && event.type != undefined
        if event.type == "submit"
          displayLoader($('.submit_form', event.currentTarget).parent())
        else
          displayLoader($('.submit_form').parent()) if event.target.type == "submit"
  $(document).ajaxError (event, jqXHR, settings) ->
    if isAccessDenied(jqXHR.responseText)
      redirectToLogin()
  $(document).ajaxSuccess (event, xhr, settings) ->
    if isLoginForm(xhr.responseText)
      redirectToLogin()
