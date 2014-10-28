isLoginForm = (html) ->
  return false if typeof html == 'object'
  nbUserName = html.indexOf "_username"
  nbPassword = html.indexOf "_password"
  if nbUserName > 0 and nbPassword > 0
    true
  else
    false

redirectToLogin = ->
  Backbone.history.navigate('#', true);
  window.location.reload()
