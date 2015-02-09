widgetChannel = Backbone.Wreqr.radio.channel('widget')

widgetChannel.commands.setHandler 'init', (view) ->
  if view.options
    if view.options.multiLanguage
      Backbone.Wreqr.radio.commands.execute 'language', 'init', view
    if view.options.multiStatus
      Backbone.Wreqr.radio.commands.execute 'status', 'init', view
    if view.options.multiVersion
      Backbone.Wreqr.radio.commands.execute 'version', 'init', view
    if view.options.duplicate
      Backbone.Wreqr.radio.commands.execute 'duplicate', 'init', view

widgetChannel.commands.setHandler 'ready', (view) ->
  if view.options
    if view.options.multiLanguage
      Backbone.Wreqr.radio.commands.execute 'language', 'ready', view
    if view.options.multiStatus
      Backbone.Wreqr.radio.commands.execute 'status', 'ready', view
    if view.options.multiVersion
      Backbone.Wreqr.radio.commands.execute 'version', 'ready', view
