isAccessDenied = (text) ->
  return @isInText(text, "client.access_denied")

isLoginForm = (html) ->
  if @isInText(html, "_username") && @isInText(html, "_password")
    true
  else
    false

isInText = (text, message) ->
  return false if typeof text == 'object' || ! text
  nunberOccurence = text.indexOf message
  if nunberOccurence > 0
    true
  else
    false

redirectToLogin = ->
  window.location.reload()
