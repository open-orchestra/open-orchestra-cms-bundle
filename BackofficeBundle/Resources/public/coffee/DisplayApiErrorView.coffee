DisplayApiErrorView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'errors'
    ])
    @initializer options
    return

  initializer: (options) ->
    errors = options.errors
    if (typeof errors.error != 'undefined')
      launchNotification 'error', errors.error.message
    else
      for key,error of errors
        launchNotification 'warning', error.message
    return
)
