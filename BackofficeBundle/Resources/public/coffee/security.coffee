isAccessDenied = (jqXHR) ->
  return jqXHR.status == 401

isLoginForm = (xhr) ->
  return xhr.getResponseHeader('X-Form-Type') == 'login'

isInText = (text, message) ->
  return false if typeof text == 'object' || ! text
  nunberOccurence = text.indexOf message
  if nunberOccurence > 0
    true
  else
    false

redirectToLogin = ->
  window.location.reload()
