isLoginForm = (html) ->
  nbUserName = html.indexOf "_username"
  nbPassword = html.indexOf "_password"
  nbUserName > 0 and nbPassword > 0

redirectToLogin = ->
  Backbone.history.navigate('#', true);
  window.location.reload()
