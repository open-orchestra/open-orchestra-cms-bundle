xhrFifo = []
$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      xhrFifo.push(xhr)
      context = settings.context
      displayLoader(context.button) if context != undefined && context.button != undefined
    abortXhr: ->
      for i of xhrFifo
        xhrFifo[i].abort()
      xhrFifo = []
  $(document).ajaxError (event, jqXHR, settings) ->
    redirectToLogin() if isAccessDenied(jqXHR.responseText)
  $(document).ajaxSuccess (event, xhr, settings) ->
    redirectToLogin() if isLoginForm(xhr.responseText)
