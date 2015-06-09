xhrFifo = []
$(document).ready ->
  $.ajaxSetup
    beforeSend: (xhr, settings) ->
      @intercept(xhr.success, isLoginForm(xhr.responseText))
      @intercept(xhr.error, isAccessDenied(xhr.responseText))
      xhrFifo.push(xhr)
      context = settings.context
      displayLoader(context.button) if context != undefined && context.button != undefined
    abortXhr: ->
      for i of xhrFifo
        xhrFifo[i].abort()
      xhrFifo = []
    intercept: (method, condition) ->
      oldMethod = method
      method = do ->
        if condition
          redirectToLogin()
        else
          oldMethod.apply this, arguments
        return
