$(document).ready ->
  $.ajaxSetup
    beforeSend: ->
      if event != undefined && event.type != undefined
        if event.type == "submit"
          displayLoader($('.submit_form', event.currentTarget).parent())
        else
          displayLoader($('.submit_form').parent()) if event.target.type == "submit"
  $(document).ajaxSuccess (event, xhr, settings) ->
    if isLoginForm(xhr.responseText)
      redirectToLogin()
