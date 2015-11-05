isAccessDenied = (jqXHR) ->
  return jqXHR.status == 401

isLoginForm = (xhr) ->
  return xhr.getResponseHeader('X-Form-Type') == 'login'

redirectToLogin = ->
  window.location.reload()
