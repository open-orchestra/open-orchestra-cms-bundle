isAccessDenied = (text) ->
  return false if typeof text == 'object'
  accessDenied = text.indexOf "client.access_denied"
  if accessDenied > 0
    true
  else
    false

redirectToLogin = ->
  window.location.reload()
